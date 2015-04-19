# Architecture Description #

Sources route to services (which are virtual) and listeners are then set up to listen to a service, either permanently or via listener schedule. If a listener is always associated to a service then this is defined in the listener table. Crash switching is possible to both crash a source to a service and a service to a listener.
All schedules are held in a record based format in the service\_schedule and listener\_schedule tables. If the schedule for a service is actually event based rather than record based then each event is put in with a single day validity. is an actual output).

Users and group management is handled by the user management tools built into the MySQL database engine.

The routeing tables are persistent across a total system restart – it comes back like it was left, although any files that were playing would of course restart rather than resume where they stopped.

# Tables #
## Source Table ##
Defines parameters for all sources
|FIELD NAME|	TYPE|	DESCRIPTION|
|:---------|:----|:-----------|
|source	|varchar(10)	|The actual unique source name|
|source\_long\_name	|text|	Verbose source name|
|enabled|	tinyint(1)	|If false then source not available|
|role	|varchar(8)	|PLAYOUT or CAPTURE|
|pharos\_index|	int(11)|	Pharos index for compatability with puffin messages|
|vlc\_hostname|	varchar(64)	|Identifier for VLC |
|icon	|text	|Link to icon file used on crash panels|
|tab\_index	|int(11)	|Holding tab for crash panels|
|owner|	varchar(64)	|Defines which users can use this source|
|notes	|text|	Placeholder for general notes|

## Service Table ##

Defines parameters for all services.

|FIELD NAME|	TYPE	|DESCRIPTION|
|:---------|:-----|:----------|
|service	|varchar(10)	|The actual service name|
|service\_long\_name|	text	|Verbose service name|
|enabled	|tinyint(1)	|If false then service not available|
|locked	|tinyint(1)	|If true then routeing to this service is locked|
|current\_source|	varchar(10)|	The current source routed to this service. Used to recover after power up and for tallies.|
|tab\_index	|int(11)	|Holding tab for crash panels|
|owner	|varchar(64)|	Defines which users can use this source|
|notes	|text	|Placeholder for general notes|
|pharos\_index	|int(11)	|Pharos index for compatability with puffin messages|


## Listener Table ##

Defines parameters for all destinations

|FIELD NAME	|TYPE|	DESCRIPTION|
|:----------|:---|:-----------|
|listener	|varchar(10)	|The actual listener name|
|schedule\_listener|	varchar(10)	|The listener name referenced in the listener schedule|
|listener\_long\_name|	text	|Verbose listener name|
|enabled	|tinyint(1)	|If false then listener not available|
|locked	|tinyint(1)	|If true then routeing to this listener is locked|
|current\_service|	varchar(10)|	The current service routed to this listener. Used to recover after power up.|
|default\_service|	varchar(10)|	If this is populated and there is nothing in Current\_Service then link to this service.|
|auto\_service	|tinyint(4)	|If true then a service is automatically created for this listener.|
|role	|varchar(8)	|Can be OUTPUT, RECORD or MONITOR. If MONITOR then Auto\_Service must be true|
|pharos\_index	|int(11)|	Pharos index for compatability with puffin messages|
|vlc\_hostname	|varchar(64)|	Assumes each host only handles one listener|
|icon	|text	|Link to icon file used on crash panels|
|tab\_index|	int(11)|	Holding tab for crash panels|
|owner	|varchar(64)	|Defines which users can use this source|
|notes	|text	|Placeholder for general notes|

## Redundancy Table ##

Defines redundancy pairs. Assumes at present that redundancy is pairs, could be expanded for larger redundancy groups.

|FIELD NAME|	TYPE|	DESCRIPTION|
|:---------|:----|:-----------|
|id	|varchar(64)|	id of the SOURCE or LISTENER|
|type	|varchar(8)	|Can be SOURCE or LISTENER|
|device| varchar(64)	|physical device the source/listener is hosted on|
|pcm |varchar(64)	|alsa pcm|
|tab\_index|	int(11)	|The tab on which this pair is held|

## Service Schedule ##

This is the main table for scheduling sources to services. It is closely based on the Pharos Playtime format for CSS compatability. It is assumed that a tool will sweep this table to clear old schedule.

There are two instances for now - an active instance and a planning instance.

|FIELD NAME	|TYPE	|DESCRIPTION|
|:----------|:----|:----------|
|service\_event\_id	|bigint(20)	|The unique event identifier|
|service|	varchar(10)|	Destination Service|
|source|	varchar(10)|	Source Index|
|first\_date|	timestamp	|First date event is valid on|
|last\_date	|timestamp	|Last date event is valid on|
|days	|varchar(7)|	SMTWTFS (replace with `_` if not used)|
|start\_time|	time	|Start time for event|
|duration	|time	|Duration of event|
|start\_mode	|varchar(1)|	F=Fixed (default), V=Variable (for future expansion)|
|name	|text	|Event name from CSS|
|material\_id	|text	|Link to Material table for material to play out if source is a playout source|
|rot	|tinyint(1)|	Defines this event must be recorded if true,using details referenced by Material\_ID|
|ptt	|varchar(1)	|F=Full, D=Display, otherwise None|
|ptt\_time|	int(11)|	PTT Alarm Time (1-10 minutes)|
|owner|	varchar(64)|	Ownership data for this package|


## Listener Schedule ##

This is the main table for scheduling services to listeners. It is closely based on the Pharos Playtime format for CSS compatability. It is assumed that a tool will sweep this table to clear old schedule. If a listener is not scheduled it can still always receive a service by defining its default service in the listener table, this can be used for logging recorders etc.

There are two instances for now - an active instance and a planning instance.

|FIELD NAME	|TYPE	|DESCRIPTION|
|:----------|:----|:----------|
|listener\_event\_id|	bigint(20)|	The unique event identifier|
|listener	|varchar(10)	|Destination Service|
|service|	varchar(10)	|Source Index|
|first\_date|	timestamp	|First date event is valid on|
|last\_date	|timestamp|	Last date event is valid on|
|days|	varchar(7)|	SMTWTFS (replace with `_` if not used)|
|start\_time	|time	|Start time for event|
|duration	|time	|Duration of event|
|start\_mode	|varchar(1)|	F=Fixed (default), V=Variable (for future expansion)|
|name	|text	|Event name from CSS|
|owner	|varchar(64)	|Ownership data for this package|

## Material Table ##

This holds additional data about material used for playout and ROT. It closely follows the Pharos Playtime design for ease of compatibility with CSS or possible future integration into Pharos Mediator.

|FIELD NAME	|TYPE|	DESCRIPTION|
|:----------|:---|:-----------|
|material\_id	|varchar(20)|	The unique material identifier|
|duration	|time	|The duration of the material|
|delete\_after	|date	|Date after which material can be deleted|
|title|	varchar(256)|	The title of the material|
|file	|varchar(256)|	The URL of the file for this material|
|material\_type|	varchar(20)|	Material Type index – refers to Material Types table|
|owner|	varchar(64)	|The owner of the material|
|client\_ref|	varchar(512)|	External data from CSS|
|tx\_date|	date	|The planned first TX date|


## Asrun Table (or Log) ##

A very basic log of switching derived from the schedules and added to at each transition. Could equally well be written to log files rather than the database.

|FIELD NAME	|TYPE|	DESCRIPTION|
|:----------|:---|:-----------|
|Timestamp	|Datetime|	Time and date of change|
|Destination|	String 8|	Destination changed|
|Source|	String 8|	Source changed|
|Title	|String 256	|Title from playlist or record for this change|
|Material|	String 256|	Material for this change (if relevant)|


## Changes Table (or Log) ##

A very basic log of changes. Could equally well be written to log files rather than the database. Need to consider how to populate this and if indeed it is necessary.

|FIELD NAME|	TYPE|	DESCRIPTION|
|:---------|:----|:-----------|
|Timestamp|	Datetime	|Time and date of change|
|Change|	String 256	|Brief description of change|
|User|	String 256	|User who made the change|

## Tabs Tables ##

Defines tabs for crash panels, etc.

|FIELD NAME|	TYPE	|DESCRIPTION|
|:---------|:-----|:----------|
|tab\_index	|int(11)|	The tab index, autonumber|
|tab\_text|	varchar(20)|	Text that appears on tab|
|enabled|	tinyint(1)|	If false then tab not available|

Tables exist for

  * sources
  * services
  * listeners
  * redundant sources