import './vanilla-toast';

function toast(message, type) {
    let config = {
        title: undefined,
        position: 'top-right',
        duration: 5000,
        closable: true,
        focusable: true
    }
    if (type === 'error') {
        vt.error(message, config);
    } else if (type === 'warning') {
        vt.warn(message, config);
    } else if (type === 'success') {
        vt.success(message, config);
    } else if (type === 'info') {
        vt.info(message, config);
    }
}

export default toast;
