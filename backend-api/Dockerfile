# 1. Base image sa Python
FROM python:3.12-slim

# 2. Set working directory 
WORKDIR /app

# 3. Kopyahin ang requirements.txt at i-install ang Python packages
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# 4. Kopyahin ang buong backend application code
COPY . .

# 5. Expose port 8000 para sa FastAPI
EXPOSE 8000

# 6. Command para patakbuhin ang FastAPI gamit ang Uvicorn sa production
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]