A bunch of separate doodles that might be useful.

Organization is minimal; subfolders/subnamespaces could definitely be used, but I'm hacking at the moment, not organizing.  Steal what you like and organize it in your project however you see fit.
Namespace is um.  Hopefully soon will be moving to a FIG-compliant namespacing, but PHP namespaces are tedious in that they aren't really a hierarchy.

/accessors is a general-purpose library for implementing automagic getters and setters in classes.  It might work, but it needs optimization and I'd also like a way to cache the dynamic function calls so it could run without the library and in better time.  Working on that.  Also needs documentation because there are various requirements that the class using it must fulfill.  Work in progress.

/antispam is a general-purpose spam solution, with classes.  It can create honeypot traps, employs xsrf detection, and uses time-to-live and expiration to make sure that sessions cannot be spoofed and that forms take a minimum amount of time to fill out.  Currently working on a Bayesian filter for content detection as well.

/core is the central functionality that all the other libraries need.  At the moment, that's all loading/autoloading.

/crypt is cryptography libraries.

/csv is wrapper classes for CSV generation in various forms.

/database is system-agnostic wrappers for database functionality so other libraries in the package can use database code without worrying about how it will be implemented.

/helpers is files with various helper functions.  I suppose they should probably be made into static methods of static classes.

/image is a sketch of an image management system in CodeIgniter. Just a sketch, not production-worthy at all, not plug-and-play.

/log is some classes under development to help with aggregation of logs.  Specific at the moment; general-purpose comes later.

/misc is a catch-all for things that don't have another home.

/pagination is a general-purpose pagination library.

/parser is the start of a parser.  Specific, not general, not working, not finished.

/querystring is a class to help with the construction of URL query strings.

/random_string is a class to generate random strings of characters.

/range is several classes dealing with ranges of numbers or dates.

/set is an implementation of a Set class.  SPL has one as well, but it has more bells and whistles than was necessary.

/slugs is somewhat specific, but will eventually be a general-purpose library for turning titles into slugs for pretty URL creation.

/table is a very rough sketch of some classes to manage tabular data.  It is my hope to eventually tie this into both an HTML renderer and a CSV renderer using the CSV library.

