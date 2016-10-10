#!/bin/sh

clear
DirName=$(basename $(pwd))
ArchName=${DirName}_$(date "+%y%m%d-%H%M")
tar -zcvf ${ArchName}.tgz ./ --exclude='.svn' --exclude='*.tgz'
echo 'done'
