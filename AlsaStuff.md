#Alsa configuration notes for our sound cards

# Introduction #

We need names for VLC to be able to access our sound card. We currently use the RME HDSP 9632 and the HDSPe AES which both use the HDSP Alsa driver. This driver works for several different cards but the configs are different for different cards.

[Using the Hammerfall HDSP on Linux](http://www.linuxjournal.com/article/7024) is a good link but its really hard to find good documentation on alsa-lib.

# Details #

## RME HDSP 9632 ##

The RME HDSP 9632 has 32 mono channels, 16 in and 16 out. The channels are allocated as follows:

  * 0-7 are the ADAT channels
  * 8-9 is spdif or AES/EBU
  * 10-11 are the onboard analog pair
  * 12-15 are the analog channels on the optional daughter boards.

We want to be able to access channels as individual stereo pairs. We don't currently have any need for the ADAT channels so we will name our stereo pairs:

  * digital
  * analog
  * analog2
  * analog3

Here is the asoundrc file for this:
```
pcm.hdsp {
        type hw
        card 0
}

ctl.hdsp {
        type hw
        card 0
}

pcm.adat {
        type plug
        ttable.0.0 1
        ttable.1.1 1
        ttable.2.2 1
        ttable.3.3 1
        ttable.4.4 1
        ttable.5.5 1
        ttable.6.6 1
        ttable.7.7 1
        slave.pcm hdsp
}

pcm.digital {
    type plug
    ttable.0.8 1
    ttable.1.9 1
    slave.pcm hdsp
}

pcm.analog {
    type plug
    ttable.0.10 1
    ttable.1.11 1
    slave.pcm hdsp
}

pcm.analog2 {
    type plug
    ttable.0.12 1
    ttable.1.13 1
    slave.pcm hdsp
}

pcm.analog3 {
    type plug
    ttable.0.14 1
    ttable.1.15 1
    slave.pcm hdsp
}
```

### RME HDSPe AES ###

To be added