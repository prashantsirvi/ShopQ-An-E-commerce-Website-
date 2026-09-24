# Phase 14 — Analytics

**Status:** Complete

## Features

- Dashboard KPI cards (orders, revenue, customers, low stock)
- Chart.js **revenue line chart** (7 days)
- Chart.js **orders by status** doughnut chart
- Top products, trending views, popular searches tables
- API: `/admin/api/analytics`

## What to Test

1. Place a few orders first (creates chart data)
2. Visit `/admin/dashboard`
3. Revenue chart renders with data points
4. Status doughnut chart shows order breakdown
5. Top products table lists best sellers
6. Open `/admin/api/analytics` while logged in as admin → JSON response
