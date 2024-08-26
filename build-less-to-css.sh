#!/bin/bash

cd themes/Transition-2/less
lessc ./build.less ../css/theme.css --clean-css --source-map=../css/theme.css.map
cd ../../..
