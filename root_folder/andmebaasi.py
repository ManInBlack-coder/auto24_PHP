import pandas as pd
import mysql.connector

# Required columns
required_columns = ['url', 'brand', 'engine', 'mileage', 'fuel', 'model', 'model_short',
                    'transmission', 'year', 'bodytype', 'drive', 'price']

# Load the CSV file with error handling
try:
    data = pd.read_csv("autod.csv", encoding='utf-8', delimiter=',')  # Ensure comma as the delimiter
    data.columns = data.columns.str.strip()  # Remove any extra whitespace from headers
    print("CSV file loaded successfully!")
except FileNotFoundError:
    print("Error: The file 'autod.csv' was not found.")
    exit()
except pd.errors.ParserError:
    print("Error: The CSV file is not formatted correctly.")
    exit()

# Check for missing columns
missing_columns = [col for col in required_columns if col not in data.columns]
if missing_columns:
    print(f"Error: The CSV file is missing one or more required columns: {missing_columns}")
    print(f"Columns found in the CSV: {list(data.columns)}")
    exit()

# Connect to the MySQL database
connection = mysql.connector.connect(
    host="localhost",
    user="root",
    password="qwerty",
    database="auto24",
    use_pure=True
)

cursor = connection.cursor()

# Create table if it doesn't exist
cursor.execute("""
CREATE TABLE IF NOT EXISTS autod (
    url VARCHAR(250),
    brand VARCHAR(250),
    engine VARCHAR(250),
    mileage VARCHAR(250),
    fuel VARCHAR(250),
    model VARCHAR(250),
    model_short VARCHAR(250),
    transmission VARCHAR(250),
    year VARCHAR(250),
    bodytype VARCHAR(250),
    drive VARCHAR(250),
    price VARCHAR(250)
)""")

# Insert data into the table
insert_query = """
INSERT INTO autod VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
"""

values = [(
    row['url'], row['brand'], row['engine'], row['mileage'],
    row['fuel'], row['model'], row['model_short'], row['transmission'],
    row['year'], row['bodytype'], row['drive'], row['price']
) for _, row in data.iterrows()]

cursor.executemany(insert_query, values)
connection.commit()

# Close the connection
cursor.close()
connection.close()

print("Data successfully imported into the MySQL database!")
