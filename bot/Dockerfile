FROM node:20-alpine

# Set working directory
WORKDIR /usr/src/app

# Install system dependencies for Baileys/Pino
RUN apk add --no-cache python3 make g++

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install --production

# Copy bot files
COPY bot/ ./bot/
COPY .env .env

# Expose bot port
EXPOSE 3001

# Start the bot
CMD ["node", "bot/server.js"]
