# Memoria projecte
# The Hearth - Gastronomia a foc i flama
Aquest projecte consisteix en una plataforma de comerç electrònic per a un restaurant d'alta gamma, inspirat en l'estètica de The Macallan. L'aplicació permet la gestió de comandes, reserves de taules, aplicació de cupons de descompte i un panell d'administració complet.

# Configuració en Local
Nota important sobre Docker: Actualment, el projecte no disposa de configuració mitjançant Docker. Per a poder executar l'aplicació en un entorn local (com XAMPP o WAMP), és necessari configurar la base de datos manualment.

1.Importació de la Base de Dades
Per a poder accedir a la base de dades en local, cal importar el fitxer SQL:
Ruta del fitxer: thehearth/documentacio/thehearth.sql
Atenció: Assegura't d'importar aquest fitxer i no el que està buit.

2.Credencials d'Accés
Pots utilitzar els següents usuaris de prova per a testejar les diferents funcionalitats i rols del sistema:

usuari admin -> mmbravo3698@gmail.com -> Manel98
usuari customer -> mmbravo36@gmail.com -> Manel98

# Tecnologies Utilitzades
-Llenguatge: PHP (Arquitectura MVC).
-Base de Dades: MySQL / MariaDB.
-Frontend: HTML5, CSS3 i fulls d'estil Bootstrap.
-APIs: Integració d'ExchangeRate-API per a la conversió de divises en temps real.