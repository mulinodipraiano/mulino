import App from "next/app";
import Head from "next/head";

import "../styles/globals.css";

const MyApp = ({ Component, pageProps }) => {
  const { global } = pageProps;

  return (
    <>
      <Head></Head>
      <Component {...pageProps} />;
    </>
  );
};

export default MyApp;
