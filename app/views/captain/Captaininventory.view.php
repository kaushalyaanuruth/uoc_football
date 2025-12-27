<main class="captain inventory-page">

  <!-- TOP BAR -->
  <div class="top">
    <img class="logo" src="<?= ROOT ?>/assets/images/logo.png" alt="UOC Football Logo">
    <span class="UOC-FOOTBALL">UOC<br>FOOTBALL</span>
  </div>

  <!-- NAVBAR -->
  <nav class="menu">
    <a href="<?= ROOT ?>/captain/dashboard" class="frame-10">Home</a>
    <a href="<?= ROOT ?>/captain/schedule" class="frame-11">Schedule</a>
    <a href="<?= ROOT ?>/captain/analyze" class="frame-10">Analyze</a>
    <a href="<?= ROOT ?>/captain/attendance" class="frame-10">Attendance</a>
    <a href="<?= ROOT ?>/captain/notices" class="frame-10">Notices</a>
    <a href="<?= ROOT ?>/captain/mealplan" class="frame-10">Meal Plan</a>
  </nav>

  <!-- RIGHT ICONS -->
  <div class="right">
    <button class="notifications">
      <img src="<?= ROOT ?>/assets/images/notifications.svg" alt="">
    </button>
    <button class="avatar"></button>
  </div>

  <!-- PAGE TITLE -->
  <section class="page-title">
    <h1>Inventory Management</h1>
    <p>Manage team equipment and supplies</p>
  </section>

  <!-- SUMMARY CARDS -->
  <section class="summary-cards">
    <div class="summary-card total">
      <h4>Total Items</h4>
      <span>248</span>
    </div>

    <div class="summary-card in-use">
      <h4>Items in Use</h4>
      <span>142</span>
    </div>

    <div class="summary-card available">
      <h4>Available Items</h4>
      <span>89</span>
    </div>

    <div class="summary-card damaged">
      <h4>Damaged Items</h4>
      <span>17</span>
    </div>
  </section>

  <!-- CHARTS -->
  <section class="charts">
    <div class="chart-box">
      <h3>Equipment Usage</h3>
      <!-- keep anima SVG here -->
    </div>

    <div class="chart-box">
      <h3>Item Status Distribution</h3>
      <!-- keep anima SVG here -->
    </div>
  </section>

  <!-- INVENTORY TABLE -->
  <section class="inventory-table">
    <div class="table-header">
      <h3>Inventory Items</h3>

      <select>
        <option>All Categories</option>
        <option>Kits</option>
        <option>Balls</option>
        <option>Equipment</option>
        <option>Accessories</option>
      </select>
    </div>

    <table>
      <thead>
        <tr>
          <th>Item Name</th>
          <th>Category</th>
          <th>Quantity</th>
          <th>Status</th>
          <th>Last Updated</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>Training Jerseys</td>
          <td>Kits</td>
          <td>25</td>
          <td><span class="status available">Available</span></td>
          <td>2 hours ago</td>
          <td>✏️ 🗑</td>
        </tr>

        <tr>
          <td>Match Footballs</td>
          <td>Balls</td>
          <td>12</td>
          <td><span class="status in-use">In Use</span></td>
          <td>1 day ago</td>
          <td>✏️ 🗑</td>
        </tr>

        <tr>
          <td>Shin Guards</td>
          <td>Accessories</td>
          <td>8</td>
          <td><span class="status damaged">Damaged</span></td>
          <td>1 week ago</td>
          <td>✏️ 🗑</td>
        </tr>
      </tbody>
    </table>
  </section>

</main>
