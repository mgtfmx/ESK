import uuid

# Generate a random UUID (Version 4)
my_uuid = uuid.uuid4()

print(my_uuid)        # Standard string: '12345678-1234-5678-1234-567812345678'
print(my_uuid.hex)    # 32-character string without hyphens

