#!/bin/sh

date=$(date +%Y%m%d)

now=$(date +"%T")

curl http://localhost/leads/worker/index.php > /opt/lampp/htdocs/leads/worker/log/leads-worker-${date}.log