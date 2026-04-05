# BINGO ONLINE – Instrucciones de instalación

## Requisitos
- PHP 7.4 o superior
- Servidor web (Apache / Nginx) con soporte para sesiones PHP
- No requiere base de datos

## Estructura de archivos
```
bingo/
├── index.php          ← Pantalla de inicio (crear / unirse)
├── lobby.php          ← Sala de espera
├── game.php           ← Pantalla de juego
├── data/              ← Archivos JSON de partidas (auto-generado)
│   └── .htaccess      ← Protege el directorio
└── api/
    ├── room.php       ← API: crear sala, unirse, iniciar, salir
    └── game.php       ← API: cartón, marcar, bingo, estado
```

## Instalación
1. Copia la carpeta `bingo/` a tu servidor web (ej. `/var/www/html/bingo/`)
2. Asegúrate de que la carpeta `data/` tenga permisos de escritura:
   ```bash
   chmod 775 bingo/data/
   ```
3. Abre `http://tu-servidor/bingo/` en el navegador

## Cómo jugar
1. El **host** crea una sala e ingresa su nombre
2. Comparte el **código de 4 caracteres** con los demás jugadores
3. Los jugadores se unen con ese código
4. El host presiona **"Iniciar partida"**
5. Las fichas se generan automáticamente cada 10–15 segundos
6. Los jugadores marcan las casillas en su cartón (solo si la ficha ya salió)
7. Al completar una línea, columna o diagonal → presionar **¡BINGO!**
8. El sistema valida en el backend; si es correcto, se declara ganador

## Mecánicas
- **Fichas:** A00–E99 (500 combinaciones posibles, 5 letras × 100 números)
- **Cartón:** 5×5 con valores únicos aleatorios
- **Anti-trampa:** No se puede marcar sin que la ficha haya salido
- **Validación:** Fila, columna, diagonal principal e inversa
- **Limpieza:** Los archivos JSON se eliminan ~60 seg. después de finalizar

## Notas técnicas
- El polling es cada 3 segundos en partida, 2.5 seg. en lobby
- La generación de fichas se dispara al hacer polling (sin cronjob necesario)
- Las sesiones PHP mantienen la identidad del jugador
- XSS protegido con htmlspecialchars en todas las salidas
