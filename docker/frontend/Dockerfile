FROM node:20-alpine

WORKDIR /usr/src/app

COPY app/package*.json ./

RUN npm config set fund false \
    && npm config set audit false \
    && npm install

COPY app/ .

EXPOSE 3000

CMD ["npm", "run", "dev", "--", "--hostname", "0.0.0.0", "--port", "3000"]