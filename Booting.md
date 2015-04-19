Booting diskless edge devices

# Introduction #

Configuration for dnsmasq and pxelinux, as well as thoughts for root mounts.

# Details #

## dnsmasq ##

I initially used my home router and configured dnsmasq on it as follows:

dhcp-boot=pxelinux.0,,192.168.2.136

Debian has dnsmask as a package and if you put it on the boot server this line becomes

dhcp-boot=pxelinux.0

## tftp ##

### using the tftp server in dnsmasq ###

copy the contents of the tftpboot directory created by lh\_build to /var/ftpd.

### using tftp-hpa ###

(as root or prefixed by sudo)

```
apt-get install tftpd-hpa live-initramfs
vi /etc/default/tftpd-hpa # and enable the server
cp /usr/lib/syslinux/pxelinux.0 /var/lib/tftpboot/
cd /var/lib/tftpboot/
mkdir pxelinux.cfg
/etc/init.d/tftpd-hpa start
chmod o+r /var/lib/tftpboot/pxelinux.0 
```

# root partitions #

Possibilities:

  * whole system in the initramfs
  * root mounted over iSCSI
  * root mounted over drbd

## Whole system in the initramfs ##

some experience on gentoo that one can make a normal file system as a cpio and use it as a n initramfs. Not got this to work yet.

## System in RAM ##

Got this to work really well using the Debian live-helper. Live-helper will make all the files needed for USB, CD or netboot live CDs. Netboot expects to mount the root file system over NFS but its possible to edit the pxelinux.cfg file and get the root file system fetched from a web server and copied. this gives us a completely stand alone cow (copy on write) system where copies don't survive reboots.

Here is the pxelinux stanza:
```
LABEL linux
        MENU LABEL Start Debian Live using webserver
        kernel debian-live/i386/vmlinuz-2.6.26-1-486
        append initrd=debian-live/i386/initrd.img-2.6.26-1-486 boot=live locale=en_GB.UTF-8 keyb=uk union=aufs fetch=http://192.168.11.1/filesystem.squashfs
```
The timeout parameter in the pxelinux can be edited so a keyboard is not needed. This is in 10ths of a second and is in the prompt.cfg file.

It should be possible to sync the cow bit to get persistance.

## Building the file system ##

debootstrap will create a simple system but a little more work is needed to make it usable. The debian-live live-helper scripts are useful but not essential.

To make a minimal debian unstable:
```
mkdir fs
debootstrap --arch=i386 sid fs
```
Then, to make it useful:
```
cp /etc/resolv.conf fs/etc
cp /etc/hosts fs/etc
cp /etc/fstab fs/etc
cp -a /etc/apt fs/etc
mount proc fs/proc -t proc
chroot fs
# do stuff in the chroot jail
umount fs/proc
mksquashfs fs /var/www/filesystem.squashfs -noappend
```
The stuff to do in the chrooted fs includes editing the /etc/hostname, /etc/hosts file and /etc/fstab
for what you want, and the following:
```
apt-get update
apt-get install ssh mono-runtime libmono-zeroconf1.0-cil vlc-nox
dpkg-reconfigure console-setup
locale-gen en_GB.UTF-8
```
TODO - install AMQP .Net client, ...

You will need some kernel modules, e.g. for the soundcard. These can be added to initrd maybe, but thats not in the spirit of things, or a kernel can be apt-getted in the chroot or you can copy /lib/modules/somekernel from the dev system.

I tried adding it in in the mksquashfs but got a corrupted file system :)