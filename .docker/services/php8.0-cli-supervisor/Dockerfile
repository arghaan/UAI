FROM thecodingmachine/php:8.0-v4-cli

USER root
RUN apt-get update \
    && apt-get install -y supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

USER docker
CMD sudo /etc/init.d/supervisor start