import mysql.connector

# --- CONFIGURE YOUR DATABASE HERE ---
config = {
    "host": "localhost",
    "user": "root",           # your MySQL username
    "password": "",           # your MySQL password
    "database": "mjiphil_catalog"  # your database name
}

try:
    # Connect to the database
    conn = mysql.connector.connect(**config)
    cursor = conn.cursor()

    # Check if column exists
    cursor.execute("SHOW COLUMNS FROM order_item LIKE 'unit_price'")
    result = cursor.fetchone()

    if result:
        print("[INFO] Column 'unit_price' already exists in order_item ✅")
    else:
        print("[INFO] Adding missing column 'unit_price'...")
        cursor.execute("ALTER TABLE order_item ADD COLUMN unit_price DECIMAL(10,2) NOT NULL AFTER quantity")
        conn.commit()
        print("[SUCCESS] Column 'unit_price' added successfully! 🎉")

except mysql.connector.Error as err:
    print(f"[ERROR] {err}")

finally:
    if conn.is_connected():
        cursor.close()
        conn.close()
