/**
 * event-report.js
 * D3.js chart logic for the Event Report page.
 */

function renderReportChart() {
    const container = document.getElementById('report-chart');
    if (!container) return;
    container.innerHTML = '';

    const data = [10, 25, 45, 30, 60, 85, 92, 110, 140, 120, 180, 210, 190, 250, 310, 380, 420, 482];
    const width = 600;
    const height = 256;

    const svg = d3.select("#report-chart")
        .append("svg")
        .attr("width", "100%")
        .attr("height", "100%")
        .attr("viewBox", `0 0 ${width} ${height}`)
        .attr("preserveAspectRatio", "none");

    const x = d3.scaleLinear().domain([0, data.length - 1]).range([0, width]);
    const y = d3.scaleLinear().domain([0, 500]).range([height, 0]);

    const line = d3.line()
        .x((d, i) => x(i))
        .y(d => y(d))
        .curve(d3.curveMonotoneX);

    const area = d3.area()
        .x((d, i) => x(i))
        .y0(height)
        .y1(d => y(d))
        .curve(d3.curveMonotoneX);

    const gradient = svg.append("defs")
        .append("linearGradient")
        .attr("id", "report-gradient")
        .attr("x1", "0%").attr("y1", "0%")
        .attr("x2", "0%").attr("y2", "100%");

    gradient.append("stop").attr("offset", "0%").attr("stop-color", "#B84C2B").attr("stop-opacity", 0.3);
    gradient.append("stop").attr("offset", "100%").attr("stop-color", "#B84C2B").attr("stop-opacity", 0);

    svg.append("path").datum(data).attr("fill", "url(#report-gradient)").attr("d", area);
    svg.append("path").datum(data).attr("fill", "none").attr("stroke", "#B84C2B").attr("stroke-width", 4).attr("d", line);
}

document.addEventListener('DOMContentLoaded', () => { renderReportChart(); });
window.addEventListener('resize', () => { renderReportChart(); });
