#!/bin/sh

date=$(date +%Y%m%d)

now=$(date +"%T")

curl http://localhost/leads/worker/scripts/all/index-payment.php > /opt/lampp/htdocs/leads/worker/log/leads-payment-${date}.log
