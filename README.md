moodle-filter_teamsubmit
===================

Moodle filter which embed Team submission management UI into an assignment intro.
It enables a "Group" like team submittion features in the assignment module,
but without using any of Moodle core course group framework and API,
as those "Teams" are meant to be ephemeral, and probably change form one assignment to another on the same course.


Requirements
------------

This plugin requires Moodle 3.1+
Works only inside an Assignment module.


Changes
-------

* 2017-12-13 - Copy team lead user's submitting status, feedback, comments and feedback file to all members in the team.
* 2016-11-12 - Initial functionality, based on block_teamsubmit

Installation
------------

Install the plugin like any other plugin to folder
/filter/teamsubmit

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage
-----

First, activate the filter_teamsubmit plugin in Site Administration -> Plugins -> Filters -> Manage filters

Create a new assignment or go into an existing one. At the intro setting, use the following syntax:
[[teamsubmit,5]]

In which 5 is the limit of team members, including the leader which submit the assignment in everyone's
name and also get graded, which is copied to all other members.

If you are using the [filter Generico](https://moodle.org/plugins/filter_generico) and its
[spacial button](https://moodle.org/plugins/atto_generico) to initiate and insert the this filter's shortcode,
you should get the Generico preset from the main folder of this repository
(filter/teamsubmit/generico-preset-team_submit.txt), and install it into your system too.

Here is how the Generico syntax looks like:
{GENERICO:type="team_submit",LIMIT="5"}

Example
-------

None

Settings
--------

None

Themes
------

No special notes

Further information
-------------------

filter_teamsubmit is found in the Davidson Moodle plugins repository:
https://github.com/nadavkav/moodle-filter_teamsubmit

Report a bug or suggest an improvement:
https://github.com/nadavkav/moodle-filter_teamsubmit/issues


Moodle release support
----------------------

Due to limited resources, filter_teamsubmit is only maintained for the most recent major release of Moodle.
However, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is
without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check
and fix problems if necessary. If you encounter problems with a new major release of Moodle -
or can confirm that filter_teamsubmit still works with a new major release.


Right-to-left support
---------------------

This plugin leverages Moodle's support for right-to-left (RTL) languages. This support was added as a contribution by nadavkav.
However, we don't regularly test it with a RTL language. If you have problems with the plugin and a RTL language,
you are free to send me a pull request on github with modifications.


Copyright
---------

Davidson science education institute at the Weizmann institute, Israel.
