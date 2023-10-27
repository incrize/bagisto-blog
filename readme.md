### Business requirements

1. There can be an unlimited number of categories
2. Categories can be disabled
3. There can be an unlimited number of posts
4. Each post can be in several categories
5. Each post can have an unlimited number of tags
6. Authors can manage their posts
7. Administrator can manage all posts and categories
8. Guests can view published posts, provided that the post has at least one active category
9. Disabled categories should not be displayed to authors and guests

### API requests example

### Login

```
mutation login {
  userLogin(input:{email: "api@example.com", password: "api@example.com"}) {
    user {
      id
    }
    accessToken
    tokenType
    expiresIn
    status
    success
  }
}
```

### Create category

```
mutation {
  blogCategoryCreate(input: {title: "Category1", slug: "cat1", status:DISABLE}) {
    id
  }
}
```

### Update category

```
mutation {
  blogCategoryUpdate(input: {id: 1, status:ACTIVE}) {
    id
  }
}
```


### Create post

```
mutation {
  blogPostCreate(
    input: {title: "Category", content: "content", categories: {sync: [1]}, tags: {create: [{title: "tag1"}, {title: "tag2"}]}}
  ) {
    id
  }
}
```

### Update post

```
mutation {
  blogPostUpdate(
    input: {id: 2, published_at: "2023-05-23 13:43:32"}
  ) {
    id
  }
}
```

### Get posts

```
query blogPosts {
  blogPosts(first: 10, category_ids: [2]) {
    data {
      id
      title
      published_at
      author {
        name
        id
      }
      tags {
        id
        title
      }
      categories {
        id
        title
      }
    }
    paginatorInfo {
      total
      lastPage
      currentPage
    }
  }
}
```
