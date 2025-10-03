<style>
     .subject-card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.3s;
        text-align: center;
        height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .subject-card:hover {
        transform: translateY(-5px);
    }

    .subject-image-container {
        width: 80px;
        height: 80px;
        background-color: #f8f9fa;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        overflow: hidden;
    }

    .subject-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .subject-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 0;
        text-align: center;
        line-height: 1.3;
    }

    .brand-text {
        color: white;
        font-weight: bold;
        font-size: 1.5rem;
        text-decoration: none;
    }

    .stats-card {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .tabs-container {
        margin-bottom: 20px;
    }

    .tab-active {
        border-bottom: 3px solid #007bff;
        color: #007bff;
        font-weight: bold;
    }

    .tab-button {
        padding: 10px 0;
        text-decoration: none;
        color: #333;
        margin-right: 30px;
        display: inline-block;
        transition: all 0.3s;
    }

    .tab-button:hover {
        color: #007bff;
        text-decoration: none;
    }

    .filter-select {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 14px;
        color: #333;
        margin-bottom: 20px;
    }

    .stats-number {
        font-size: 1.2rem;
        font-weight: bold;
        color: #007bff;
    }

    .rank-text {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .progress-title {
        color: #333;
        font-size: 1.1rem;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .guest-message {
        background-color: #e3f2fd;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        color: #1976d2;
        margin-bottom: 20px;
    }

    .stats-card {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #007bff;
    }

    .stats-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .stats-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .stats-value {
        font-weight: 600;
        color: #333;
    }

    .score-badge {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #dee2e6;
    }

    .history-card {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #007bff;
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .history-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .history-date {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .score-badge {
        background-color: #e8f5e8;
        color: #2e7d32;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .score-badge-low {
        background-color: #fff3e0;
        color: #f57c00;
    }

    .score-badge-medium {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .history-details {
        display: flex;
        gap: 20px;
        font-size: 0.9rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #6c757d;
    }

    .detail-value {
        font-weight: 600;
        color: #333;
    }

    .correct-count {
        color: #2e7d32;
    }

    .wrong-count {
        color: #d32f2f;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #dee2e6;
    }

    .pagination {
        justify-content: center;
        margin-top: 30px;
    }
</style>