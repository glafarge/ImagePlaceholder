Image placeholder
=================

A quick and simple self hosted image placeholder service inspired by placehold.it

It generates FPO images on the fly.


FPO
---
FPO means For Position Only.

FPO is the placement of a blank placeholder or a temporary low-resolution illustration in the required location and size on the camera ready artwork to indicate where an actual image is to be placed on the final film or plate.


Usage
-----

Copy files to your webserver. Then, put parameters after your URL and you'll get a placeholder :)

```html
<!-- Size configuration ONLY -->
<img src="http://myplaceholder.local/150x100" /> 

<!-- Square image 150x150 -->
<img src="http://myplaceholder.local/150" /> 

<!-- Size + colors (background color / text color) -->
<img src="http://myplaceholder.local/150x100/333333/dddddd" />

<!-- Size + custom text -->
<img src="http://myplaceholder.local/150x100?text=Prysme+placeholder" />
```


Additional parameters
---------------------

Add `?forceDownload` to force image download.


Requirements
------------

You need PHP 5.3 with the GD library (activated) and the mod_rewrite (for pretty urls).


_____________________________________________

(c) PRYSME 2012 - http://prys.me