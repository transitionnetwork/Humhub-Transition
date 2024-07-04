#!/bin/bash

cd themes/Transition-2/less
lessc ./build.less ../css/theme.css --clean-css="--s1 --advanced" --source-map=../css/theme.css.map
cd ../../..
