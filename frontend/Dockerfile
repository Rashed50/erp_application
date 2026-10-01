# Stage 1: Build the Vue application
FROM node:24-alpine AS build-stage

WORKDIR /app

# Copy package files and install dependencies
COPY package*.json ./
RUN npm install

# Copy source code and build for production
COPY . .
RUN npm run build

# Stage 2: Serve using Nginx for local testing
FROM nginx:alpine AS production-stage

# Copy custom Nginx configuration if needed, or use default public dir
COPY --from=build-stage /app/dist /usr/share/nginx/html

# Copy custom Nginx config for Vue Router SPA fallback
RUN echo $'server {\n\
    listen 80;\n\
    location / {\n\
        root /usr/share/nginx/html;\n\
        index index.html;\n\
        try_files $uri $uri/ /index.html;\n\
    }\n\
}' > /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]