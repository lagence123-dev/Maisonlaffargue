# Módulo de Retirada de Pedidos para PrestaShop

## Descripción General
El Módulo de Retirada de Pedidos permite a los clientes ejercer su derecho de desistimiento de pedidos en cumplimiento con las leyes de protección al consumidor de la UE. El formulario público usa por defecto nombre del cliente + identificador de pedido, registra la solicitud para revisión manual y envía confirmaciones por correo electrónico.

## Características
- **Cumplimiento con la UE**: Totalmente conforme con la Directiva 2011/83/UE de la UE sobre derechos de los consumidores
- **Creación Automática de Estado de Pedido**: Crea automáticamente el estado de pedido "Retirada Solicitada" durante la instalación
- **Cálculo Automático del Período**: Calcula el período de desistimiento basado en la fecha del pedido y el tiempo estimado de entrega
- **Interfaz Amigable para el Cliente**: Formulario adaptado a la marca en el que la clientela envía su desistimiento
- **Notificaciones por Correo Electrónico**: Notificaciones automáticas tanto para clientes como para administradores
- **CTA Localizadas**: Textos predeterminados para ocho idiomas con posibilidad de sobrescritura por idioma
- **Panel de Administración Moderno**: Área de configuración inspirada en shadcn con integración del color corporativo (#f1b218)
- **Compatible con PrestaShop 8/9**: Funcionamiento verificado en versiones recientes, incluido PS 9 con nueva gestión de locales
- **Soporte Multiidioma**: Disponible en alemán, inglés, francés, italiano, español, polaco, rumano y neerlandés
- **Configuración Personalizable**: Período de desistimiento y tiempos de entrega configurables
- **Verificación Flexible**: Nombre + identificador de pedido recomendado, identificador solo opcional y e-mail + identificador como modo legacy
- **Revisión Manual Predeterminada**: Las nuevas instalaciones registran las solicitudes para evaluación en el Back Office

## Requisitos
- PrestaShop 1.7.8 o superior (probado con PrestaShop 8.x y 9.x)
- PHP 7.4 o superior (PHP 8.x compatible)
- Certificado SSL recomendado para envíos seguros de formularios

## Instalación

### Método 1: Vía Panel de Administración de PrestaShop
1. Descargue el archivo ZIP del módulo
2. Navegue a **Módulos** → **Gestor de Módulos** en su administración de PrestaShop
3. Haga clic en **Subir un módulo**
4. Seleccione el archivo ZIP y súbalo
5. El módulo se instalará automáticamente
6. Durante la instalación, se crea automáticamente un nuevo estado de pedido **"Retirada Solicitada"**

### Método 2: Instalación Manual
1. Descargue y extraiga los archivos del módulo
2. Suba la carpeta `orderwithdrawl` al directorio `/modules/`
3. Navegue a **Módulos** → **Gestor de Módulos**
4. Busque "Order Withdrawal"
5. Haga clic en **Instalar**
6. Durante la instalación, se crea automáticamente un nuevo estado de pedido **"Retirada Solicitada"**

**Nota**: El módulo crea automáticamente el estado de pedido "Retirada Solicitada" con color naranja (#FFA500) durante la instalación. Este estado se utiliza para rastrear pedidos donde los clientes han solicitado el desistimiento.

## Configuración

Después de la instalación, configure el módulo haciendo clic en **Configurar**:

### Configuración General
- **Estado del módulo**: Activar o desactivar el módulo
- **Período de desistimiento (días)**: Definir el plazo legal (predeterminado: 14 días)
- **Tiempo estimado de entrega (días)**: Tiempo medio que se utiliza para calcular la fecha límite definitiva
- **Verificación del cliente**: Recomendado nombre + identificador de pedido; opcional solo identificador; legacy e-mail + identificador
- **Modo de evaluación**: Revisión manual predeterminada o comprobación automática del período
- **Correos electrónicos adicionales del administrador**: Lista separada por comas de destinatarios extra para las notificaciones

### Experiencia del Cliente
- **Gestión de la etiqueta del CTA**: Definir el texto del botón por idioma con valores predeterminados incluidos
- **Posición de visualización**: Mostrar el CTA en el pie de página, en el menú de cuenta de cliente o en ambos
- **Estilo y color del pie de página**: Alternar entre botón/enlace y ajustar el color del botón a la marca

## Uso

### Para Clientes
1. Haga clic en el enlace **"Desistir del contrato"** en la sección Mi Cuenta (generalmente visible en el área del pie de página)
2. Se abre el formulario de solicitud de desistimiento
3. Introduzca el **identificador de pedido configurado** y, por defecto, su **nombre**. Algunas tiendas pueden usar solo identificador o el modo legacy con e-mail.
4. Haga clic en **Enviar Solicitud de Desistimiento**
5. El sistema valida:
   - El pedido existe y coincide con el modo de verificación configurado
   - El período de desistimiento se comprueba automáticamente solo si la tienda usa ese modo
6. Tras el envío exitoso:
   - Recibe mensaje de confirmación en pantalla
   - Recibe correo electrónico de confirmación
   - El estado del pedido cambia a "Retirada Solicitada" para revisión manual

### Para Administradores
1. Las solicitudes de desistimiento activan notificaciones por correo electrónico
2. El estado del pedido cambia a "Retirada Solicitada" (si está configurado)
3. Procese los reembolsos según la política de su tienda
4. Actualice el estado del pedido en consecuencia

## Plantillas de Correo Electrónico

El módulo envía correos electrónicos automáticos para:
- **Confirmación del Cliente**: Se envía cuando se envía la solicitud de desistimiento
- **Notificación del Administrador**: Se envía al administrador(es) de la tienda cuando se recibe la solicitud

## Resolución de Problemas

### Problemas Comunes

**Mensaje "El período de desistimiento ha expirado"**
- El período de desistimiento ha pasado basado en la fecha del pedido + tiempo de entrega + días de desistimiento
- Verifique su configuración

**Error "Estado de desistimiento no configurado"**
- Este error no debería ocurrir si el módulo se instaló correctamente
- El estado "Retirada Solicitada" se crea automáticamente durante la instalación
- Si aparece este error, intente reinstalar el módulo

**Notificaciones por correo electrónico no recibidas**
- Verifique la configuración de correo electrónico en PrestaShop
- Revise las carpetas de spam
- Asegúrese de que las direcciones de correo electrónico estén correctamente formateadas

## Cumplimiento Legal

Este módulo ayuda a cumplir con:
- **Directiva 2011/83/UE de la UE** sobre derechos de los consumidores
- **BGB alemán § 355** (Widerrufsrecht)
- Implementaciones nacionales de las leyes de protección al consumidor de la UE

**Importante**: Este módulo proporciona el marco técnico para solicitudes de desistimiento. Los comerciantes son responsables de:
- Procesar reembolsos dentro de los plazos legales
- Mantener la documentación adecuada
- Garantizar el cumplimiento legal completo con las regulaciones locales

## Capturas de Pantalla

1. **Página de Configuración del Módulo** (`img/en/screenshot_1_config.png`)
   - Muestra la interfaz de configuración principal con todas las configuraciones visibles

2. **Sección de Pie de Página de Mi Cuenta** (`img/en/screenshot_2_footer_link.png`)
   - Muestra el enlace "Desistir del contrato" en la sección Mi Cuenta/pie de página

3. **Formulario de Solicitud de Desistimiento** (`img/en/screenshot_3_withdrawal_form.png`)
   - Muestra el formulario donde los clientes introducen el identificador de pedido y los datos requeridos por el modo de verificación

4. **Cambio de Estado del Pedido** (`img/en/screenshot_8_order_status.png`)
   - Muestra un pedido con el estado "Retirada Solicitada" en el panel de administración

## Soporte

Para soporte técnico, utilice el canal de soporte del marketplace asociado a su pedido del módulo.
- Preguntas legales: Consulte con su asesor legal
- Actualizaciones del módulo: Consulte la página del módulo regularmente

## Registro de Cambios

### Versión 1.4.0 (junio de 2026)
- Nueva opción opcional en Back Office para mostrar productos del pedido después de verificar los datos del cliente
- El cliente puede seleccionar el pedido completo o artículos concretos con una cantidad válida
- La lista de productos se carga del lado del servidor y no utiliza AJAX
- La opción está desactivada por defecto y conserva los ajustes existentes
- Sin cambios en el esquema de base de datos; compatibilidad mantenida con PrestaShop 1.7.8, 8.x y 9.x con PHP 7.4+

### Versión 1.3.1 (mayo de 2026)
- Versión pública 1.3.1 con soporte estructurado para desistimientos parciales
- El formulario público permite elegir entre desistir del pedido completo o de artículos/servicios seleccionados
- El desistimiento parcial usa filas repetibles con nombre del artículo, número del artículo y cantidad
- No se solicita un motivo de desistimiento; los campos solo identifican el contrato o la parte del contrato
- Los correos de recepción para cliente y administrador incluyen el alcance del desistimiento y los detalles enviados
- Los detalles del desistimiento parcial se guardan como mensaje del pedido para revisión en Back Office
- Sin cambios en el esquema de base de datos; se conservan los ajustes existentes

### Versión 1.3.0 (mayo de 2026)
- Lanzamiento público 1.3.0
- Revisión manual predeterminada para nuevas instalaciones
- Verificación recomendada por nombre del cliente + identificador de pedido
- Modo opcional solo identificador y modo legacy e-mail + identificador
- El formulario público ya no muestra el bloque `<details>` de política de desistimiento
- Los trabajos internos anteriores de actualización 1.3.x se integraron en `upgrade-1.3.0.php`

**Actualización de 1.2.4 a 1.3.0:**
- Ruta directa de actualización desde 1.2.4 a 1.3.0; los cambios internos anteriores de 1.3.x se incluyen en esta versión pública.
- Sin cambios en el esquema de base de datos; se conservan los ajustes existentes del comerciante.
- Nuevos valores predeterminados faltantes: revisión manual, verificación por nombre + identificador de pedido, búsqueda por número/referencia de pedido y fallback PHP mail activo.
- Los correos de recepción confirman la solicitud de desistimiento y los datos enviados, sin sugerir una decisión final.
- Nuevas opciones en Back Office para verificación del cliente, búsqueda de pedido, fallback de correo, vista previa de correo, envío de prueba y restablecimiento.
- Formulario público reforzado: token CSRF, honeypot, límite por IP, prevención de duplicados y notas de cliente seguras para HTML.
- Envío de correo más fiable con MailDispatcher central, diagnósticos y fallback PHP mail.
- Plantillas de correo y traducciones completadas para todos los idiomas incluidos.

### Versión 1.0.0 (2024)
- Lanzamiento inicial
- Creación automática del estado de pedido "Retirada Solicitada"
- Soporte multiidioma (8 idiomas)
- Sistema de notificación por correo electrónico
- Períodos de desistimiento configurables
- Cumplimiento con la directiva de la UE

## Licencia

Este módulo es software propietario. Todos los derechos reservados.

---

*Este módulo se proporciona tal cual. Si bien tiene como objetivo ayudar con el cumplimiento de la UE, los comerciantes son en última instancia responsables de garantizar que su tienda cumpla con todos los requisitos legales.*
