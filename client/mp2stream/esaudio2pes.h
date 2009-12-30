#ifndef _ESAUDIO2PES
#define _ESAUDIO2PES
#include <stdint.h>

size_t padding(unsigned char* es_frame, size_t es_frame_size);
size_t mkPESheader(unsigned char* pes_header, unsigned char* es_frame,
                 size_t es_frame_size, int stream_id, uint64_t pts);
void stamp_ts (unsigned long long int ts, unsigned char* buffer);
#endif
