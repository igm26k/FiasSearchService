FROM ubuntu:18.04

ARG SPHINX_VERSION=3.1.1-612d99f

ENV SPHINX_VERSION=${SPHINX_VERSION}

VOLUME ["/opt/sphinx/conf/"]
VOLUME ["/opt/sphinx/data"]

RUN echo 'begin' \
    && apt-get update \
    && apt-get -y install \
        curl \
        jq \
        nginx \
        mysql-client \
        libmysqlclient-dev \
        supervisor \
    && echo "Start download sphinxsearch (${SPHINX_VERSION}) ==" \
    && mkdir -p /opt/sphinx /opt/sphinx/conf \
    && curl -sSL \
        http://sphinxsearch.com/files/sphinx-${SPHINX_VERSION}-linux-amd64.tar.gz \
        | tar -C /opt/sphinx  -zxf - \
    && echo 'check indexer' \
    && PATH="${PATH}:/opt/sphinx/sphinx-${SPHINX_VERSION%-*}/bin" indexer -v \
    && echo "clean image" \
    && apt-get autoremove -y \
    && apt-get autoclean \
    && rm -r /var/lib/apt/lists/* \
    && echo 'end'

COPY sphinx-example.conf /opt/sphinx/conf/sphinx-example.conf
COPY ./docker/sphinxsearch/consul-template/  /etc/consul-template/
COPY ./docker/sphinxsearch/supervisor        /etc/supervisor
COPY ./docker/sphinxsearch/nginx/            /etc/nginx

EXPOSE 9312 9306

COPY ./docker/sphinxsearch/bin/ /usr/local/bin

ENTRYPOINT [ "/usr/local/bin/docker-entrypoint.sh" ]












COPY docker/sphinxsearch/bin/wait_for_mysql.sh /usr/local/bin/wait_for_mysql.sh

RUN chmod 0777 /usr/local/bin/wait_for_mysql.sh

# sphinxsearch config
COPY sphinxsearch/sphinx.conf        /opt/sphinx/conf/sphinx.conf
COPY sphinxsearch/base.conf          /opt/sphinx/conf/base.conf
COPY sphinxsearch/idxFiasHouse.conf  /opt/sphinx/conf/idxFiasHouse.conf
COPY sphinxsearch/idxFiasObject.conf /opt/sphinx/conf/idxFiasObject.conf
#COPY sphinxsearch/idxAddress.conf    /opt/sphinx/conf/idxAddress.conf

# update indexes
COPY docker/sphinxsearch/bin/updateIndexes.sh /usr/local/bin/updateIndexes.sh
COPY docker/sphinxsearch/supervisor/conf.d-avaliable/sphinx-updateIndexes.conf /etc/supervisor/conf.d/sphinx-updateIndexes.conf

# Nginx
COPY docker/sphinxsearch/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
