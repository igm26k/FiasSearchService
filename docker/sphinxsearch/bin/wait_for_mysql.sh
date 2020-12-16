#!/usr/bin/env bash

# set -e

# check jq
jq --version > /dev/null
if [ $? -ne 0 ];then >2& echo "jq error"; exit 1 ; fi;

getSecret(){
	local secrets_file=/secrets/secrets.json
	local __resultvar=$1
	local secret_name=$1
	if [ -f "${secrets_file}" ]; then
		local secret_val=$(jq ".data.${secret_name}" ${secrets_file})
	else
		echo "error, secrets_file (${secrets_file}) not found"
		local secret_val="${secret_name}"
	fi
	eval $__resultvar=${secret_val}
}

getSecret MYSQL_DB_HOST
getSecret MYSQL_DB_USER
getSecret MYSQL_DB_PASS
getSecret MYSQL_DB_PORT

for i in `seq 1 10`
do
	mysqladmin ping -h${MYSQL_DB_HOST} -P${MYSQL_DB_PORT} -u${MYSQL_DB_USER} -p${MYSQL_DB_PASS} --connect-timeout 5
	result=$?
	if [ "${result}" -eq "0" ]; then
		>&2 echo "MySQL is up - executing command"
		break
	else
		>&2 echo "MySQL is unavailable - sleeping"
		sleep 6
		echo "Retry.. ${i}"
	fi
done

if [ "${result}" -ne "0" ]; then
	>&2 echo "ERROR: MySQL is unavailable - exit"
	exit 1
fi
