# blogging-with-bear

## A set of scripts for exporting notes from [Bear](http://www.bear-writer.com) to Github

By Landon Borges

## How to use

The general idea is:

1. Tag some of your Bear notes as `public`
2. Datestamp each of these notes by placing an H6 heading somewhere in each note that contains the output of the "Insert Current Date" command in Bear (Cmd-Shift-7)
3. From your Mac's Terminal, run `bear-export.php`

This extracts the public-tagged notes, formats them as a static HTML site, and pushes them to [Github](https://github.com/).  

You will need to change some variables in `config.php` to make this work for your particular setup.

## Live Example
Check out an example blog site created entirely from this workflow: https://blog.glassvillage.io/

## Acknowledgements

Uses "Parsedown" by Emanuil Rusev:

The MIT License (MIT)

Copyright (c) 2013-2018 Emanuil Rusev, erusev.com

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

