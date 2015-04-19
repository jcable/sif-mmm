snippets of vlc syntax

# Introduction #

Hard to get just audio


# Details #
e.g.

cvlc -vv v4l2:// :v4l2-adev="hw:0,0" --sout '#transcode{acodec=mpga,ab=192,channels=2}:std{access=udp,mux=ts,dst=224.0.0.1:5001}' -I telnet