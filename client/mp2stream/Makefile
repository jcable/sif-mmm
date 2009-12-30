LIBS=-lasound -ltwolame
mp2stream: mp2stream.o pesaudio2ts.o esaudio2pes.o
	g++ $^ -o $@ $(LIBS)
