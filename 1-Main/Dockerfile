# Step 1: Use an official nginx image as the base image
FROM nginx:alpine

# Step 2: Set the working directory in the container
WORKDIR /usr/share/nginx/html

# Step 3: Copy the frontend file to the nginx html folder
COPY front.html ./index.html

# Step 4: Expose port 80 to the outside world
EXPOSE 80

# Step 5: Start nginx server (this is automatically handled by nginx image)
