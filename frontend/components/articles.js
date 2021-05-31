import React from "react";
import Card from "./card";

const Articles = ({ articles }) => {
  return (
    <div>
      {articles.map((article) => {
        return <Card article={article} key={article.slug} />;
      })}
    </div>
  );
};

export default Articles;
