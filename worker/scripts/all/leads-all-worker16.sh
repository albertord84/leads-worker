#!/bin/sh

date=$(date +%Y%m%d)

now=$(date +"%T")

curl http://localhost/leads/worker/scripts/all/index-do.php?id=16 > /opt/lampp/htdocs/leads/worker/log/leads-robot16-${date}.log
