module.exports = {
  apps: [
    // {
    //   // --- โปรเซสสำหรับ Laravel Octane ---
    //   name: 'hskwarrior-octane',
    //   script: 'artisan',
    //   args: 'octane:start --host=127.0.0.1 --port=1215', // <-- ดึงมาจากข้อมูลเดิม
    //   interpreter: 'php',
    //   cwd: '/var/www/hskwarrior.com', // <-- ระบุ Path ที่ถูกต้อง
    //   exec_mode: 'fork',
    //   autorestart: true,
    //   watch: false,
    // },
    {
      // --- โปรเซสสำหรับ Inertia SSR ---
      name: 'hskwarrior-ssr',
      script: 'artisan',
      args: 'inertia:start-ssr',
      interpreter: 'php',
      cwd: '/var/www/hskwarrior.com', // <-- ระบุ Path ที่ถูกต้อง
      exec_mode: 'fork',
      autorestart: true,
      watch: false,
    }
  ]
};
