FROM redis:6.2.3-alpine

RUN mkdir -p /usr/local/etc/redis
COPY files/redis.conf /usr/local/etc/redis/redis.conf

CMD ["redis-server", "/usr/local/etc/redis/redis.conf"]
