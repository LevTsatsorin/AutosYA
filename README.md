# Consigna Final

## Instalación

### 1️⃣ Base de Datos

1. Abrir http://localhost/phpmyadmin
2. Importar base de datos llamada `alquiler_autos`
3. Importar el archivo: `bd/alquiler_autos.sql`

### 2️⃣ Crear Usuario Administrador

**Generar Hash de Contraseña**

Crear archivo temporal `generar_hash.php` en la raíz:

```php
<?php
echo password_hash('admin123', PASSWORD_BCRYPT);
?>
```

Acceder a: http://localhost/AutosYA/generar_hash.php  
Copiar el hash generado

**Insertar Admin en DB**

Desde phpMyAdmin:

```sql
INSERT INTO usuarios (nombre, correo, clave, fk_rol)
VALUES ('Admin', 'admin@autosya.com', 'HASH_GENERADO', 1);
```

### 3️⃣ Iniciar Servidor

1. Abrir **XAMPP**
2. Click en **Start** para:
   - Apache
   - MySQL

### 4️⃣ Acceder al Sistema

http://localhost/AutosYA

---

## 🔐 Credenciales de Prueba

### Administrador (recomendado):

- **Correo**: admin@autosya.com
- **Contraseña**: admin123

### Cliente (créalo tú mismo)

---

**Autors**: Lev Tsatsorin, Ainur Munasipov  
**Grupo**: ACT2AP
