<style lang="scss">
.design-list {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: stretch;
    gap: var(--gap);
    @include viewport-props((
        --items-in-row: ('': 3, '<sm': 1),
        --gap: relSize(1rem, $min: 1rem),
    ));
    &__item {
        width: 128px;
    }
}
</style>