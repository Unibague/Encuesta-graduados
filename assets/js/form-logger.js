/**
 * Módulo de logging para formulario de registro de graduados
 * Registra eventos y errores en el servidor para debugging
 */

const FormLogger = {
    apiUrl: '/api/log-formulario.php',
    
    /**
     * Registra un evento en el servidor
     */
    async log(eventType, data = {}) {
        try {
            const payload = {
                type: eventType,
                section: data.section || null,
                field: data.field || null,
                status: data.status || null,
                message: data.message || null,
                error: data.error || null,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                userAgent: navigator.userAgent
            };

            await fetch(this.apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).catch(() => {}); // Silencioso si falla
        } catch (e) {
            console.error('Error logging form event:', e);
        }
    },

    /**
     * Registra inicio del formulario
     */
    formStarted() {
        this.log('formulario_iniciado', { 
            message: 'Formulario de registro cargado',
            browser: this.getBrowser()
        });
    },

    /**
     * Registra cambio de sección
     */
    sectionChange(sectionTitle, sectionIndex, totalSections) {
        this.log('cambio_seccion', {
            section: sectionTitle,
            current: sectionIndex + 1,
            total: totalSections,
            message: `Avanzando a sección ${sectionIndex + 1} de ${totalSections}`
        });
    },

    /**
     * Registra validación fallida
     */
    validationFailed(sectionTitle, missingFields) {
        this.log('validacion_fallida', {
            section: sectionTitle,
            fields: missingFields.join(', '),
            message: `Validación fallida en: ${missingFields.join(', ')}`
        });
    },

    /**
     * Registra inicio de verificación
     */
    verificationStart(cedula) {
        this.log('verificacion_iniciada', {
            message: `Verificando cédula: ${cedula.substring(0, 4)}****`,
            field: 'cedula_verificacion'
        });
    },

    /**
     * Registra verificación exitosa
     */
    verificationSuccess(cedula, status) {
        this.log('verificacion_exitosa', {
            status: status,
            message: 'Graduado verificado correctamente',
            field: 'cedula_verificacion'
        });
    },

    /**
     * Registra verificación fallida
     */
    verificationFailed(cedula, status, reason) {
        this.log('verificacion_fallida', {
            status: status,
            message: reason || 'Cédula no encontrada',
            error: reason,
            field: 'cedula_verificacion'
        });
    },

    /**
     * Registra error de verificación
     */
    verificationError(errorMessage) {
        this.log('verificacion_error', {
            error: errorMessage,
            message: 'Error durante la verificación',
            field: 'cedula_verificacion'
        });
    },

    /**
     * Registra inicio de envío
     */
    submitStart() {
        this.log('envio_iniciado', {
            message: 'Iniciando envío del formulario'
        });
    },

    /**
     * Registra envío exitoso
     */
    submitSuccess() {
        this.log('envio_exitoso', {
            message: 'Formulario enviado correctamente',
            status: 'success'
        });
    },

    /**
     * Registra error de envío
     */
    submitError(errorMessage) {
        this.log('envio_error', {
            error: errorMessage,
            message: 'Error durante el envío del formulario',
            status: 'error'
        });
    },

    /**
     * Registra cambio de campo
     */
    fieldChange(fieldName, value) {
        // Solo registrar cambios importantes
        if (['pais', 'ciudad', 'estado_laboral', 'nivel_academico'].includes(fieldName)) {
            this.log('cambio_campo', {
                field: fieldName,
                message: `Campo ${fieldName} modificado`
            });
        }
    },

    /**
     * Registra error de JavaScript
     */
    jsError(message, filename, lineno, colno, error) {
        this.log('error_js', {
            error: `${message} en ${filename}:${lineno}:${colno}`,
            message: message,
            status: 'error'
        });
    },

    /**
     * Detecta el navegador
     */
    getBrowser() {
        const ua = navigator.userAgent;
        if (ua.includes('Chrome') && !ua.includes('Chromium')) return 'Chrome';
        if (ua.includes('Safari') && !ua.includes('Chrome')) return 'Safari';
        if (ua.includes('Firefox')) return 'Firefox';
        if (ua.includes('Edge')) return 'Edge';
        if (ua.includes('Opera')) return 'Opera';
        return 'Unknown';
    },

    /**
     * Detecta si es iOS
     */
    isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent);
    },

    /**
     * Detecta si es Android
     */
    isAndroid() {
        return /Android/.test(navigator.userAgent);
    }
};

// Capturar errores globales
window.addEventListener('error', (event) => {
    FormLogger.jsError(
        event.message,
        event.filename,
        event.lineno,
        event.colno,
        event.error
    );
});

// Capturar rechazos de promesas no manejadas
window.addEventListener('unhandledrejection', (event) => {
    FormLogger.log('promesa_rechazada', {
        error: event.reason?.message || String(event.reason),
        message: 'Promesa rechazada sin manejo'
    });
});
