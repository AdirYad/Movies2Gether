import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faPlay,
    faPause,
    faExpand,
    faCompress,
    faVolumeUp,
    faVolumeDown,
    faVolumeMute,
    faPaperPlane,
} from "@fortawesome/free-solid-svg-icons";
import {
} from "@fortawesome/free-brands-svg-icons";
import {
} from "@fortawesome/free-regular-svg-icons";

export default {
  icons: library.add(
    // Solid
    faPlay,
    faPause,
    faExpand,
    faCompress,
    faVolumeUp,
    faVolumeDown,
    faVolumeMute,
    faPaperPlane,

    // Brands

    // Regular
  ),
};
