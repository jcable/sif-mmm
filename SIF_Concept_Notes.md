SIF concepts – notes by Mark Patrick, 05/06/2009

Enabled fields:
These just set whether a source, service or listener is shown on the GUI. If it exists, then it can theoretically be used by one of the switching mechanisms. Normally the second part of a resilient pair of listeners would not be enabled. Enabled is ignored by the monitoring panels. Also OFF is always shown as a source, even if not set up or enabled.

Redundancy:
Redundany pairs are possible for sources and listeners. For listeners, when a switch is made to the main listener, a check is made to see if it is in a pair and if it is then the same switch is made to the reserve. If a switch is made directly to the reserve for some reason, this doesn’t change the main. For sources, there is a panel which will change the active field. When this changes, any service connected to other one of the pair is updated. Also, the active field is updated and this dictates what is seen on the crash panels. The tab\_index is only required for source tab pairs, so to prevent them showing on the panel, don’t enable listener pairs

Data Integrity:
At present there is no forced referential integrity, however all the PHP pages enforce their own integirty where relevant. My thought is that bad data will never cause anything to crash, but it may cause a non-switch.

Holding:
If a crash switch is made with the hold set, this asserts the locked field for the service or listener. Scheduling should then be ignored for this.

Maintenance Tools:
It is possible to set up the tabs used in the crash panels for sources, services, listeners and redundancy. Each individual source, service, listener or redundancy pair then has to be set up to be on that tab. It is possible to set up the source, services, listeners and redundancy pairs from the maintenance menu. The concept for these is that when adding a new one, you put in the name which adds it to the table, then you go and edit its details. For redundancy pairs, you have to also specify if it is a source or listener pair as this can not then be edited.

Messaging:
It is assumed that any task or tool which makes a switch will send a TCP message to indicate this so that all devices can then check for a change which may be relevant to them. This needs to be defined. At present the web crash panels do not refresh – ideally this messaging would refresh them.



Tools:
It is assumed that there will be a tools as follows:
•	to sweep old schedule out of the database
•	to create the as run logs
•	to monitor for crash switches and update any other tools as needed

It is assumed that all listeners will check for their own scheduled events.

Not Implemented:
•	Auto services – ned to work on this but assuming that a source can be set as the service for a listener that is defined as an autoservice, don’t know how to show these on the crash panels as yet.
•	Default services – nee to think about what tool will use this.