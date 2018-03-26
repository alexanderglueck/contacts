<template>
  <ais-index
    app-id="3IP2YWI7XB"
    api-key="4177f0ab169df092a028ea7b5c28392f"
    index-name="link_index"
  >
    <ais-search-box></ais-search-box>
    <ais-results>
      <template scope="{ result }">
        <h2>
          <ais-highlight :result="result" attribute-name="name"></ais-highlight>
        </h2>
      </template>
    </ais-results>
  </ais-index>
</template>