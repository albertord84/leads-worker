#!/bin/sh

date=$(date +%Y%m%d)

now=$(date +"%T")

curl http://localhost/leads/worker/scripts/all/index-do.php?id=15 > /opt/lampp/htdocs/leads/worker/log/leads-robot15-${date}.log
