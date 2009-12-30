#include <string>
#include <iostream>
#include <cstdlib>
#include <alsa/asoundlib.h>
#include <twolame.h>
#include "esaudio2pes.h"

using namespace std;

#define MP2BUFSIZE	(16384)

snd_pcm_t *alsa_init(const string& dev, unsigned int& sample_rate, unsigned int channels)
{
    int err;
    snd_pcm_t *capture_handle;
    snd_pcm_hw_params_t *hw_params;

    if ((err = snd_pcm_open (&capture_handle, dev.c_str(), SND_PCM_STREAM_CAPTURE, 0)) < 0) {
        cerr << "cannot open audio device " << dev << " (" << snd_strerror (err) << ")" << endl;
        exit (1);
    }

    if ((err = snd_pcm_hw_params_malloc (&hw_params)) < 0) {
        cerr << "cannot allocate hardware parameter structure (" << snd_strerror (err) << ")" << endl;
        exit (1);
    }

    if ((err = snd_pcm_hw_params_any (capture_handle, hw_params)) < 0) {
        cerr << "cannot initialize hardware parameter structure (" << snd_strerror (err) << ")" << endl;
        exit (1);
    }

    if ((err = snd_pcm_hw_params_set_access (capture_handle, hw_params, SND_PCM_ACCESS_RW_INTERLEAVED)) < 0) {
        cerr << "cannot set access type (" << snd_strerror (err) << ")" << endl;
        exit (1);
    }

    if ((err = snd_pcm_hw_params_set_format (capture_handle, hw_params, SND_PCM_FORMAT_S16_LE)) < 0) {
        cerr << "cannot set sample format (" << snd_strerror (err) << ")" << endl;
        exit (1);
    }

    if ((err = snd_pcm_hw_params_set_rate_near (capture_handle, hw_params, &sample_rate, 0)) < 0) {
        cerr << "cannot set sample rate (" << snd_strerror (err) << ")" << endl;
        exit (1);
    }

    if ((err = snd_pcm_hw_params_set_channels (capture_handle, hw_params, channels)) < 0) {
        cerr << "cannot set channel count (" << snd_strerror (err) << ")" << endl;
        exit (1);
    }

    if ((err = snd_pcm_hw_params (capture_handle, hw_params)) < 0) {
        cerr << "cannot set parameters (" << snd_strerror (err) << ")" << endl;
        exit (1);
    }

    snd_pcm_hw_params_free (hw_params);

    if ((err = snd_pcm_prepare (capture_handle)) < 0) {
        cerr << "cannot prepare audio interface for use (" << snd_strerror (err) << ")" << endl;
        exit (1);
    }
    return capture_handle;
}

struct PCR
{
    uint64_t _27MHz;
    uint16_t _90kHz;
};

class TSPacket
{
public:
    TSPacket();
    ~TSPacket();
    size_t putFirst(unsigned char* pes_header, size_t pes_header_size, 
	unsigned char* mp2buffer, size_t mp2fill_size, bool disc, FILE* outfile);
    size_t put(unsigned char* mp2buffer, size_t mp2fill_size, FILE* outfile);
    uint8_t cc;
    uint16_t pid;
};

TSPacket::TSPacket():cc(0),pid(0x1fff)
{
}

TSPacket::~TSPacket()
{
}

size_t TSPacket::putFirst(unsigned char* pes_header, size_t pes_header_size, 
	unsigned char* mp2buffer, size_t mp2fill_size, bool disc, FILE* outfile)
{
    unsigned char* tspacket[188];
    memset(tspacket, 0, TS_PACKET_SIZE);
    tspacket[0] = 0x47;
    tspacket[1] = ((pid >> 8) & 0x1f) | 0x40;
    tspacket[2] = pid & 0xff;
    tspacket[3] = 0x30 | (cc & 0x0f);
    tspacket[4] = (8+33+6+9+(disc?8:0))/8;
    tspacket[5] = 0x50 | (disc?0x84:0); // PCR present | random access | disc?(disc|splice)


}

size_t TSPacket::put(unsigned char* mp2buffer, size_t mp2fill_size, FILE* outfile)
{
    unsigned char* tspacket[188];
    memset(tspacket, 0, TS_PACKET_SIZE);
    tspacket[0] = 0x47;
    tspacket[1] = (pid >> 8) & 0x1f;
    tspacket[2] = pid & 0xff;
    tspacket[3] = 0x10 | (cc & 0x0f);
}

void mkTSpackets(unsigned char* pes_header, size_t pes_header_size, 
	unsigned char* mp2buffer, size_t mp2fill_size, bool disc, FILE* outfile)
{
    int left=pes_header_size+mp2fill_size;
    size_t taken = mkTSFirstpacket(pes_header, pes_header_size, mp2buffer, mp2fill_size, disc, outfile);
    while(left>0)
    {
        left -= mkTSpacket(mp2buffer, mp2fill_size, outfile);
    }
}

main (int argc, char *argv[])
{
    int err;
    unsigned int sample_rate = 48000;
    unsigned int channels = 2;
    unsigned int bit_rate = 192;
    string device="default";
    string outputfilename="a.pes";
    size_t num_frames=100;
    FILE* outfile;
    snd_pcm_t* capture_handle = alsa_init(device, sample_rate, channels);
    twolame_options *encodeOptions;
    short int *pcmaudio;
    unsigned char *mp2buffer;
    int mp2fill_size=0;
    int frames=0;
    int stream_id=192;
    uint64_t pts_offset = 0;

    /* Allocate some space for the PCM audio data */
    if ( (pcmaudio = new short[TWOLAME_SAMPLES_PER_FRAME*channels])==NULL) {
        cerr << "pcmaudio alloc failed" << endl;
        exit(1);
    }

    /* Allocate some space for the encoded MP2 audio data */
    if ( (mp2buffer=(unsigned char *)calloc(MP2BUFSIZE, sizeof(unsigned char)))==NULL) {
        cerr << "mp2buffer alloc failed" << endl;
        exit(1);
    }


    /* grab a set of default encode options */
    encodeOptions = twolame_init();
    cerr << "Using libtwolame version " << get_twolame_version() << endl;


    // Use sound file to over-ride preferences for
    // mono/stereo and sampling-frequency
    twolame_set_num_channels(encodeOptions, channels);
    if (channels == 1) {
        twolame_set_mode(encodeOptions, TWOLAME_MONO);
    } else {
        twolame_set_mode(encodeOptions, TWOLAME_STEREO);
    }


    /* Set the input and output sample rate to the same */
    twolame_set_in_samplerate(encodeOptions, sample_rate);
    twolame_set_out_samplerate(encodeOptions, sample_rate);

    /* Set the bitrate */
    twolame_set_bitrate(encodeOptions, bit_rate);


    /* initialise twolame with this set of options */
    if (twolame_init_params( encodeOptions ) != 0) {
        cerr << "Error: configuring libtwolame encoder failed." << endl;
        exit(1);
    }

    /* Open the output file for the encoded MP2 data */
    if ((outfile = fopen(outputfilename.c_str(), "wb"))==0) {
        cerr << "error opening output file " << outputfilename << endl;
        exit(1);
    }

    uint64_t pts = pts_offset;
    unsigned char nulls[180];
    memset(nulls, 0, sizeof(nulls));

    for (size_t i = 0; i < num_frames; ++i) {

    // Read num_samples of	audio data *per channel* from the input file

        if ((err = snd_pcm_readi (capture_handle, pcmaudio, TWOLAME_SAMPLES_PER_FRAME)) != TWOLAME_SAMPLES_PER_FRAME) {
            cerr << "read from audio interface failed (" << snd_strerror (err) << ")" << endl;
            exit (1);
        }

	unsigned char pes_header[20];

        mp2fill_size = twolame_encode_buffer_interleaved(encodeOptions, pcmaudio, TWOLAME_SAMPLES_PER_FRAME, mp2buffer, MP2BUFSIZE);

	size_t phb = mkPESheader(pes_header, mp2buffer, mp2fill_size, stream_id, pts);

        mkTSpackets(pes_header, phb, mp2buffer, mp2fill_size, false, outfile);

	pts += (TWOLAME_SAMPLES_PER_FRAME * 90000) / sample_rate;


        // Display the number of MPEG audio frames we have encoded
        frames++;
        fprintf(stdout,"[%04i] [%04d]\r", frames, mp2fill_size);
        fflush(stdout);
    }

    /* flush any remaining audio. (don't send any new audio data)
       There should only ever be a max of 1 frame on a flush.
       There may be zero frames if the audio data was an exact
       multiple of 1152 */
    mp2fill_size = twolame_encode_flush(encodeOptions, mp2buffer, MP2BUFSIZE);
    fwrite(mp2buffer, sizeof(unsigned char), mp2fill_size, outfile);


    snd_pcm_close (capture_handle);
    twolame_close( &encodeOptions );
    free(pcmaudio);

    cout << endl << "Finished nicely." << endl;


    return(0);
}
