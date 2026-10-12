import fs from 'fs';
import path from 'path';
import uni from '@dcloudio/vite-plugin-uni';
import { defineConfig } from 'vite';

// uni-app 内部按绝对路径解析源码目录。若 UNI_INPUT_DIR 为相对路径（如 "./"），
// 构建时 static 目录会被误判忽略、无法复制到产物（app.json 中 tabBar 图标即因此报"未找到"）。
// 因此在加载插件前统一转为绝对路径。
process.env.UNI_INPUT_DIR = path.resolve(process.env.UNI_INPUT_DIR || './');

export default defineConfig({
  css: {
    preprocessorOptions: {
      scss: {
        silenceDeprecations: ['import', 'global-builtin', 'legacy-js-api'],
      },
    },
  },
  plugins: [
    uni(),
    {
      // 模板静态资源（如 /static/bg1.png）会被 uni 编译管线提取到独立的 common/assets.js
      // chunk，它只被组件 require、不在 app.js 依赖链中，导致微信开发者工具模块表未收录
      //（运行时 require 报 "module 'common/assets.js' is not defined"）。
      // assets.js 是构建期生成物，无法在源码中 import，因此构建完成后手动把
      // require("./common/assets.js") 挂到 app.js（与入口其它依赖同一收录机制）。
      name: 'uni-ensure-assets-chunk',
      enforce: 'post',
      apply: 'build',
      closeBundle() {
        // uni CLI 通过 UNI_OUTPUT_DIR 注入产物目录（如 unpackage/dist/build/mp-weixin）
        const outDir = process.env.UNI_OUTPUT_DIR || 'unpackage/dist/build/mp-weixin';
        const appJs = path.join(outDir, 'app.js');
        if (!fs.existsSync(appJs)) return;
        const src = fs.readFileSync(appJs, 'utf8');
        if (src.includes('require("./common/assets.js")')) return;

        // 压缩产物首行为："use strict";Object.defineProperty(...);const e=require("./common/vendor.js"),o=require(...),...
        // 注意：不能在 require("./common/vendor.js") 后直接追加分号，否则会把逗号相连的 const 声明链
        // 截断（",o=..." 变成无声明赋值），在严格模式下抛出 "ReferenceError: o is not defined"。
        // 因此改为在行首 "use strict"; 之后注入 require，保持原有声明链完整。
        let fixed;
        if (/^"use strict";/.test(src)) {
          fixed = src.replace(/^"use strict";/, '"use strict";require("./common/assets.js");');
        } else {
          // 兼容无 "use strict" 前缀的产物：将 vendor require 后的逗号替换为分号 + require，并补回 const 声明
          fixed = src.replace(
            /(require\("\.\/common\/vendor\.js"\)),( )?([A-Za-z_$][\w$]*)(=)/,
            '$1;require("./common/assets.js");const $3$4'
          );
        }
        if (fixed !== src) {
          fs.writeFileSync(appJs, fixed);
        }
      },
    },
  ],
});
