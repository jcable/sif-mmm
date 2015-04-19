#Notes on how server and edge device discovery can work

# Goal #

On boot we want edge devices to get a hostname (or hostnames) based on their role. A new device should put a task into the admin task queue (so we need one of those) to assign a permanent hostname, for example based on the studio name it is the audio source for.

The edge device should be able to discover database/web servers using dns-sd. Because we want to have no single point of failure there could be more than one server.

Once the edge device can talk to the server it can download additional information from it by keying using its mac address to get its hostname, etc (if this didn't work properly via DHCP), using its sound card PCI id to get alsa config data, etc.

# dnsmasq #

dnsmasq can read its mac to hostname and ip mappings from a simple flat file. It can call a script everytime it gives or withdraws and ip address.

So we can detect new devices coming on board and run a server script to enter the device in the SQL database and enter a config task in the admin queue.

To make this network friendly, the script should discover the server via dns-sd and use a web service (or sql) to do this.

We could skip this step and let the edge device do the reporting. This might be better, since the server might be global. So we could have the edge device report directly to the server and then when the edge device is fully configured it could invoke a web service on the dns/dhcp server to update the dchp-hosts entry.

# edge device scripting #

dnsmasq avoids some of the need for edge device scripting but not all. If we assume that the DHCP process makes sure that the database has an entry for the new machine then the edge device still needs to provide cpu and sound card (and other hw like SDI/ASI interfaces).

The edge device script goes something like:

  1. discover database using dns-sd
  1. look up mac address in device table - should have a column to indicate if fully configured or not.
  1. if not fully configured, supply cpu and pci info (ram ? writeable storage ?) and trigger server to configure the db (and pxe) for this edge device.
  1. fetch the config data and deploy it
  1. run the edge device application (maybe this is done by init.d)

# Admin Task #

The admin task queue should lead to a page where a permanent host name is associated with the mac address. This should also write an entry to the dnsmasq dhcp-host file and SIGHUP dnsmasq. Note that this implies the db is co-hosted with dnsmasq but we could use a web service on the dns/dhcp server to network this.

It should be possible to push a hostname change to the edge device by calling a web service on the edge device to avoid the need to reboot the edge device.

It should also be possible to configure, or reconfigure the edge device specific configuration in terms of sound card data, etc.

# pxe #

We will want to deliver different kernel/initrd/root file systems to different hosts. For example generation 1 edge devices have via C7 CPUs while in Debian need 486 kernels. Intel Atom based edge devices should get 686 kernels, Core2Duo CPUs should get amd64 kernels, gumstix should get arm kernels, ...

As part of the discovery edge script we could call a server script to create a specific pxe boot script for that device. This would also speed boot.