#!/bin/bash
rm -Rf ./api
mkdir api
doxygen ./docs_api.dxy
open -a Safari api/index.html
