const fs = require('fs');
const path = require('path');

const src = path.join(__dirname, '..', 'storage', 'app', 'public');
const dest = path.join(__dirname, '..', 'public', 'storage');

function normalize(p) {
    try {
        return fs.realpathSync(p).toLowerCase().replace(/^[\\/?]+/, '').replace(/^\\\\\?\\/, '');
    } catch {
        return p.toLowerCase();
    }
}

try {
    if (fs.existsSync(src)) {
        if (fs.existsSync(dest) && normalize(src) === normalize(dest)) {
            console.log('✓ public/storage is already linked locally');
            process.exit(0);
        }

        if (!fs.existsSync(dest)) {
            fs.mkdirSync(dest, { recursive: true });
        }

        fs.cpSync(src, dest, { recursive: true, force: true });
        console.log('✓ Successfully copied storage files to public/storage');
    }
} catch (e) {
    console.error('Warning: could not copy storage files:', e.message);
}
