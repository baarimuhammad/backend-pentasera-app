/**
 * event-report.js
 * D3.js chart logic for the Event Report page.
 * Uses window.__dailySales data (from PHP) for real chart data.
 * Implements CSV export from window.__recentTransactions.
 */

function renderReportChart() {
    const container = document.getElementById('report-chart');
    if (!container) return;
    container.innerHTML = '';

    const width = 600;
    const height = 256;
    const padding = { top: 20, right: 20, bottom: 40, left: 20 };

    const svg = d3.select("#report-chart")
        .append("svg")
        .attr("width", "100%")
        .attr("height", "100%")
        .attr("viewBox", `0 0 ${width} ${height}`)
        .attr("preserveAspectRatio", "xMidYMid meet");

    // Use real daily_sales data if available, otherwise fallback
    const rawData = window.__dailySales || [];
    let data;
    let dateLabels;

    if (rawData.length > 0) {
        data = rawData.map(d => parseInt(d.orders) || 0);
        dateLabels = rawData.map(d => {
            const dt = new Date(d.date);
            return dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
        });
    } else {
        // Fallback dummy data
        data = [10, 25, 45, 30, 60, 85, 92, 110, 140, 120, 180, 210, 190, 250, 310, 380, 420, 482];
        dateLabels = data.map((_, i) => `Hari ${i + 1}`);
    }

    if (data.length === 0) {
        svg.append("text")
            .attr("x", width / 2)
            .attr("y", height / 2)
            .attr("text-anchor", "middle")
            .attr("fill", "#999")
            .attr("font-size", "14px")
            .text("Belum ada data penjualan");
        return;
    }

    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;

    const x = d3.scaleLinear().domain([0, data.length - 1]).range([padding.left, width - padding.right]);
    const y = d3.scaleLinear().domain([0, d3.max(data) || 1]).range([height - padding.bottom, padding.top]);

    const line = d3.line()
        .x((d, i) => x(i))
        .y(d => y(d))
        .curve(d3.curveMonotoneX);

    const area = d3.area()
        .x((d, i) => x(i))
        .y0(height - padding.bottom)
        .y1(d => y(d))
        .curve(d3.curveMonotoneX);

    // Gradient
    const gradient = svg.append("defs")
        .append("linearGradient")
        .attr("id", "report-gradient")
        .attr("x1", "0%").attr("y1", "0%")
        .attr("x2", "0%").attr("y2", "100%");

    gradient.append("stop").attr("offset", "0%").attr("stop-color", "#B84C2B").attr("stop-opacity", 0.3);
    gradient.append("stop").attr("offset", "100%").attr("stop-color", "#B84C2B").attr("stop-opacity", 0);

    // Area + line
    svg.append("path").datum(data).attr("fill", "url(#report-gradient)").attr("d", area);
    svg.append("path").datum(data).attr("fill", "none").attr("stroke", "#B84C2B").attr("stroke-width", 3).attr("d", line);

    // Data points
    const maxPoints = Math.min(data.length, 8);
    const step = Math.max(1, Math.floor((data.length - 1) / (maxPoints - 1)));
    const pointIndices = [];
    for (let i = 0; i < data.length; i += step) {
        pointIndices.push(i);
    }
    if (!pointIndices.includes(data.length - 1)) {
        pointIndices.push(data.length - 1);
    }

    svg.selectAll('.dot')
        .data(pointIndices)
        .enter().append('circle').attr('class', 'dot')
        .attr('cx', i => x(i))
        .attr('cy', i => y(data[i]))
        .attr('r', 4)
        .attr('fill', 'white')
        .attr('stroke', '#B84C2B')
        .attr('stroke-width', 2);

    // X-axis date labels
    const labelIndices = pointIndices.slice(0, 6);
    svg.selectAll('.x-label')
        .data(labelIndices)
        .enter().append('text')
        .attr('class', 'x-label')
        .attr('x', i => x(i))
        .attr('y', height - 5)
        .attr('text-anchor', 'middle')
        .attr('fill', '#999')
        .attr('font-size', '9px')
        .attr('font-weight', 'bold')
        .text(i => dateLabels[i] || '');
}

/**
 * Export CSV from window.__recentTransactions
 */
function exportCSV() {
    const transactions = window.__recentTransactions || [];
    const eventName = window.__eventName || 'Event';

    if (transactions.length === 0) {
        alert('Belum ada data transaksi untuk diekspor.');
        return;
    }

    // CSV header
    const headers = ['Kode Order', 'Nama Pembeli', 'Email', 'Tiket', 'Jumlah', 'Total (Rp)', 'Tanggal'];

    // CSV rows
    const rows = transactions.map(tx => [
        tx.order_code || '-',
        `"${(tx.buyer_name || '-').replace(/"/g, '""')}"`,
        tx.buyer_email || '-',
        `"${(tx.tickets || '-').replace(/"/g, '""')}"`,
        tx.qty || 0,
        tx.total || 0,
        tx.date || '-',
    ]);

    // Build CSV string
    let csv = '\uFEFF'; // BOM for Excel UTF-8 compatibility
    csv += headers.join(',') + '\n';
    rows.forEach(row => {
        csv += row.join(',') + '\n';
    });

    // Create download
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `laporan-${eventName.replace(/[^a-zA-Z0-9]/g, '-')}-${new Date().toISOString().slice(0,10)}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// ── Init ──
document.addEventListener('DOMContentLoaded', () => { renderReportChart(); });
window.addEventListener('resize', () => { renderReportChart(); });
