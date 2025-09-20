<template>
  <div class="page-header">
    <div class="page-block">
      <div class="row align-items-center">
        <div class="col-md-12">
          <div class="page-header-title">
            <h5 class="m-b-10 px-2">{{ pageTitle }}</h5>
          </div>
          <ul class="breadcrumb">
            <!-- Home link -->
            <li class="breadcrumb-item">
              <a href="/admin/dashboard">Home</a>
            </li>
            
            <!-- Dynamic breadcrumb items -->
            <li 
              v-for="(item, index) in items" 
              :key="index"
              class="breadcrumb-item"
              :class="{ 'active': index === items.length - 1 }"
              :aria-current="index === items.length - 1 ? 'page' : null"
            >
              <a 
                v-if="item.url && index !== items.length - 1" 
                :href="item.url"
              >
                {{ item.text }}
              </a>
              <span v-else>{{ item.text }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Breadcrumb',
  props: {
    pageTitle: {
      type: String,
      default: 'Dashboard'
    },
    items: {
      type: Array,
      default: () => []
    }
  }
}
</script>

<style scoped>
.page-header {
  background: #fff;
  padding: 20px 0;
  border-bottom: 1px solid #e9ecef;
}

.page-header-title h5 {
  color: #495057;
  font-weight: 600;
}

.breadcrumb {
  background: transparent;
  padding: 0;
  margin: 0;
}

.breadcrumb-item {
  font-size: 14px;
}

.breadcrumb-item a {
  color: #007bff;
  text-decoration: none;
}

.breadcrumb-item a:hover {
  color: #0056b3;
  text-decoration: underline;
}

.breadcrumb-item.active {
  color: #6c757d;
}

.breadcrumb-item + .breadcrumb-item::before {
  content: ">";
  color: #6c757d;
  padding: 0 8px;
}
</style>