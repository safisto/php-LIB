#!/bin/bash

cd ${remote.files.dir}/${id}
unzip ${release.zip} -d release

cd ${remote.app.dir}
mv LIB LIB.old
mv ${remote.files.dir}/${id}/release/* .
rm -rf LIB.old

cd ${remote.files.dir}
rm -rf ${id}*
