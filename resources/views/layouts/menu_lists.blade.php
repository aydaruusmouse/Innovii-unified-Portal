<li class="pc-item">
  <a href="/dashboard" class="pc-link">
    <span class="pc-micon">
      <i data-feather="home"></i>
    </span>
    <span class="pc-mtext" style="font-size: 16px;">Dashboard</span>
  </a>
</li>

<li class="pc-item pc-caption">
  <label style="font-size: 14px;">Subscriber Reports</label>
  <i data-feather="sidebar"></i>
</li>
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i data-feather="bar-chart"></i>
    </span>
    <span class="pc-mtext" style="font-size: 14.5px;">SDF Reports</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('all_services') }}" style="font-size: 14px;">Service Overview</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('single_service') }}" class="pc-link" style="font-size: 14px;">New Subscribers Analytics</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('status_wise_services') }}" class="pc-link" style="font-size: 14px;">Detailed report by status</a>
    </li>
   
    <li class="pc-item">
      <a href="{{ route('overall_subscriber_report') }}" class="pc-link" style="font-size: 14px;">Overall Subscription Status</a>
    </li>
  </ul>
</li>

<!-- <li class="pc-item pc-caption">
  <label style="font-size: 14px;">Voicemail Reports</label>
  <i data-feather="sidebar"></i>
</li>
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i data-feather="phone"></i>
    </span>
    <span class="pc-mtext" style="font-size: 14.5px;">Voicemail Reports</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item"><a class="pc-link" href="#!" style="font-size: 14px;">Level 2.1</a></li>
    <li class="pc-item pc-hasmenu">
      <a href="#!" class="pc-link" style="font-size: 14px;">Level 2.2<span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
      <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="#!" style="font-size: 12px;">Level 3.1</a></li>
        <li class="pc-item"><a class="pc-link" href="#!" style="font-size: 12px;">Level 3.2</a></li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link" style="font-size: 12px;">Level 3.3<span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!" style="font-size: 12px;">Level 4.1</a></li>
            <li class="pc-item"><a class="pc-link" href="#!" style="font-size: 12px;">Level 4.2</a></li>
          </ul>
        </li>
      </ul>
    </li>
    <li class="pc-item pc-hasmenu">
      <a href="#!" class="pc-link" style="font-size: 14px;">Level 2.3<span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
      <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="#!" style="font-size: 12px;">Level 3.1</a></li>
        <li class="pc-item"><a class="pc-link" href="#!" style="font-size: 12px;">Level 3.2</a></li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link" style="font-size: 12px;">Level 3.3<span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!" style="font-size: 12px;">Level 4.1</a></li>
            <li class="pc-item"><a class="pc-link" href="#!" style="font-size: 12px;">Level 4.2</a></li>
          </ul>
        </li>
      </ul>
    </li>
  </ul>
</li> -->


<li class="pc-item pc-caption">
  <label style="font-size: 14px;">CRBT Reports</label>
  <i data-feather="sidebar"></i>
</li>
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i data-feather="music"></i>
    </span>
    <span class="pc-mtext" style="font-size: 14.5px;">CRBT Core Reports</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('crbt.daily_mis') }}" style="font-size: 14px;">Daily CRBT MIS</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('crbt.hourly_mis') }}" class="pc-link" style="font-size: 14px;">Hourly CRBT MIS</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('crbt.interface_sub_unsub') }}" class="pc-link" style="font-size: 14px;">Interface-wise Sub/Unsub</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('crbt.interface_tone') }}" class="pc-link" style="font-size: 14px;">Interface-wise Tone Usage</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('crbt.status_cycle') }}" class="pc-link" style="font-size: 14px;">Status Cycle MIS</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('crbt.hlr_activations') }}" class="pc-link" style="font-size: 14px;">HLR Activations</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('crbt.user_info') }}" class="pc-link" style="font-size: 14px;">User Information</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('crbt.user_tone_info') }}" class="pc-link" style="font-size: 14px;">User Tone Information</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('crbt.billing_charges') }}" class="pc-link" style="font-size: 14px;">Billing & Charges</a>
    </li>
  </ul>
</li>

<!-- <li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i data-feather="briefcase"></i>
    </span>
    <span class="pc-mtext" style="font-size: 14.5px;">Corporate CRBT Reports</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('crbt.corporate_info') }}" style="font-size: 14px;">Corporate Accounts</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('crbt.corporate_users') }}" class="pc-link" style="font-size: 14px;">Corporate Users</a>
    </li>
  </ul>
</li>

<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i data-feather="database"></i>
    </span>
    <span class="pc-mtext" style="font-size: 14.5px;">Backup Reports</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('crbt.backup_reports') }}" style="font-size: 14px;">CRBT Core Backup</a>
    </li>
  </ul>
</li> -->

<!-- <li class="pc-item pc-caption">
  <label style="font-size: 14px;">System Management</label>
  <i data-feather="sidebar"></i>
</li>
<li class="pc-item">
  <a href="/admin/users" class="pc-link">
    <span class="pc-micon">
      <i data-feather="users"></i>
    </span>
    <span class="pc-mtext" style="font-size: 16px;">User Management</span>
  </a>
</li>
<li class="pc-item">
  <a href="/admin/settings" class="pc-link">
    <span class="pc-micon">
      <i data-feather="settings"></i>
    </span>
    <span class="pc-mtext" style="font-size: 16px;">System Settings</span>
  </a>
</li>
<li class="pc-item">
  <a href="/admin/roles" class="pc-link">
    <span class="pc-micon">
      <i data-feather="shield"></i>
    </span>
    <span class="pc-mtext" style="font-size: 16px;">Role & Permissions</span>
  </a>
</li>
<li class="pc-item">
  <a href="/admin/audit" class="pc-link">
    <span class="pc-micon">
      <i data-feather="eye"></i>
    </span>
    <span class="pc-mtext" style="font-size: 16px;">Audit Logs</span>
  </a>
</li> -->

<li class="pc-item pc-caption">
  <label style="font-size: 14px;">Emergency Credit Reports</label>
  <i data-feather="sidebar"></i>
</li>
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i data-feather="credit-card"></i>
    </span>
    <span class="pc-mtext" style="font-size: 14.5px;">Emergency Credit Report</span>
    <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('emergency_credit.daily') }}" style="font-size: 14px;">Daily Transactions</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('emergency_credit.top_users') }}" class="pc-link" style="font-size: 14px;">Top Users</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('emergency_credit.weekly') }}" class="pc-link" style="font-size: 14px;">Weekly Trends</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('emergency_credit.monthly') }}" class="pc-link" style="font-size: 14px;">Monthly Overview</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('emergency_credit.status') }}" class="pc-link" style="font-size: 14px;">Transaction Status</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('emergency_credit.credit_type') }}" class="pc-link" style="font-size: 14px;">Credit Type Analysis</a>
    </li>
    <li class="pc-item pc-caption">
      <label style="font-size: 12px; color: #666;">Revenue Reports</label>
    </li>
    <li class="pc-item">
      <a href="{{ route('emergency_credit.revenue_summary') }}" class="pc-link" style="font-size: 14px;">Revenue Summary (All)</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('emergency_credit.revenue_data_only') }}" class="pc-link" style="font-size: 14px;">Revenue Data Only</a>
    </li>
    <li class="pc-item">
      <a href="{{ route('emergency_credit.revenue_with_balance') }}" class="pc-link" style="font-size: 14px;">Revenue with Balance</a>
    </li>
  </ul>
</li>
