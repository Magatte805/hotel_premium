FROM node:20-alpine

WORKDIR /app

# Install deps first (better layer caching)
COPY package*.json ./
RUN npm ci

# App source
COPY . .

EXPOSE 5173

# Vite dev server (Docker needs --host)
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "5173"]




