# Base on offical Node.js Alpine image
FROM node:alpine

# Set working directory
WORKDIR /usr/app

# Copy package.json and package-lock.json before other files
# Utilise Docker cache to save re-installing dependencies if unchanged
COPY ./frontend/package*.json ./

# Install dependencies
RUN npm install --production

# Copy all files
COPY ./frontend/ ./

# Build app
RUN npm run build

# Expose the listening port
EXPOSE 3000 

# Run container as non-root (unprivileged) user
# The node user is provided in the Node.js Alpine base image
USER node

# RUN npm install -g pm2
# ENV PATH="${PATH}:/usr/app/node_modules/pm2/bin"

# Run npm start script when container starts
CMD [ "npm", "start" ]
# CMD [ "pm2-runtime", "start", "./ecosystem.config.js" ]
