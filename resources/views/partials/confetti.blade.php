{{-- Petite animation de confettis en pur JS/Canvas, sans dépendance externe.
     Appel : triggerConfetti() depuis n'importe quelle page qui inclut ce partial. --}}
<canvas id="confetti-canvas" style="position: fixed; inset: 0; pointer-events: none; z-index: 10000; display: none;"></canvas>

<script>
    function triggerConfetti() {
        const canvas = document.getElementById('confetti-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        canvas.style.display = 'block';

        const colors = ['#6366F1', '#22C55E', '#F59E0B', '#EF4444', '#EC4899', '#0EA5E9'];
        const pieces = [];
        const count = 140;

        for (let i = 0; i < count; i++) {
            pieces.push({
                x: Math.random() * canvas.width,
                y: -20 - Math.random() * canvas.height * 0.5,
                w: 6 + Math.random() * 6,
                h: 8 + Math.random() * 10,
                color: colors[Math.floor(Math.random() * colors.length)],
                speedY: 2 + Math.random() * 3,
                speedX: (Math.random() - 0.5) * 2,
                rotation: Math.random() * 360,
                rotationSpeed: (Math.random() - 0.5) * 10,
            });
        }

        let frame = 0;
        const maxFrames = 220;

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            pieces.forEach(p => {
                p.x += p.speedX;
                p.y += p.speedY;
                p.rotation += p.rotationSpeed;

                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate((p.rotation * Math.PI) / 180);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                ctx.restore();
            });

            frame++;
            if (frame < maxFrames) {
                requestAnimationFrame(draw);
            } else {
                canvas.style.display = 'none';
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        }

        draw();
    }

    window.addEventListener('resize', () => {
        const canvas = document.getElementById('confetti-canvas');
        if (canvas) {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
    });

    // Compteurs animés : tout élément avec data-counter="N" défile de 0 à N au chargement
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-counter]').forEach(el => {
            const target = parseInt(el.getAttribute('data-counter'), 10) || 0;
            const duration = 900;
            const startTime = performance.now();

            function step(now) {
                const progress = Math.min(1, (now - startTime) / duration);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(eased * target);
                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            }
            requestAnimationFrame(step);
        });
    });
</script>