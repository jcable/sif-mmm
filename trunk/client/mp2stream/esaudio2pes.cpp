/*
 * Copyright (C) 2004  Lorenzo Pallara, lpallara@cineca.it
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2.1
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

#include <netinet/in.h>

#include <fcntl.h>
#include <unistd.h>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <sys/types.h>
#include "esaudio2pes.h"

#define PES_HEADER_SIZE 14
#define PACK_AUDIO_FRAME_INCREASE 8
#define ES_HEADER_SIZE 2
#define PTS_MAX 8589934592LL

void stamp_ts (unsigned long long int ts, unsigned char* buffer)
{
    if (buffer) {
        buffer[0] = ((ts >> 29) & 0x0F) | 0x01;
        buffer[1] = (ts >> 22) & 0xFF;
        buffer[2] = ((ts >> 14) & 0xFF ) | 0x01;
        buffer[3] = (ts >> 7) & 0xFF;
        buffer[4] = ((ts << 1) & 0xFF ) | 0x01;
    }
}

size_t padding(unsigned char* es_frame, size_t es_frame_size)
{
    bool ismpeg2 = (es_frame[0] == 0xFF) && ((es_frame[1] >> 5)== 0x07);
    int bytes = 0;
    if (ismpeg2 && (((es_frame[2] & 0x3) >> 1) > 0)) {
        /* check layer */
        int layer = es_frame[1] & 0x6 >> 1;
        if (layer == 2) {
            bytes = 4;
        } else {
            bytes = 1;
        }
    } else {
        bytes = 0;
    }
    return bytes;
}

size_t mkPESheader(unsigned char* pes_header, unsigned char* es_frame,
                 size_t es_frame_size, int stream_id, uint64_t pts)
{
    bool ismpeg2 = (es_frame[0] == 0xFF) && ((es_frame[1] >> 5)== 0x07);
    if (!((ismpeg2 && es_frame[0] == 0xFF && (es_frame[1] >> 5) == 0x07) ||
            (!ismpeg2 && (es_frame[0] == 0x0B) && (es_frame[1] == 0x77)))) {
        return 0;
    }

    pes_header[0] = 0x00;
    pes_header[1] = 0x00;
    pes_header[2] = 0x01;
    pes_header[3] = stream_id;

    /* check for padding */
    ((uint16_t*)&pes_header[4])[0] = htons( es_frame_size + PACK_AUDIO_FRAME_INCREASE);

    pes_header[6] = 0x81; /* no scrambling, no priority, no alignment defined, no copyright, copy */
    pes_header[7] = 0x80; /* pts, no escr, no es rate, no dsm trick, no extension flag, no additional copy info, no crc flag */
    pes_header[8] = 0x05; /* pts */
    stamp_ts (pts % PTS_MAX, &pes_header[9]);
    pes_header[9] &= 0x0F;
    pes_header[9] |= 0x20;

    return PES_HEADER_SIZE;
}

